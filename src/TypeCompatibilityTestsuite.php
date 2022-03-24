<?php

namespace Midnight\PhpTypesystemTests;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function explode;
use function fclose;
use function fgets;
use function fopen;
use function preg_match;
use function sprintf;
use function str_starts_with;
use function substr;
use function trim;

final class TypeCompatibilityTestsuite
{
    public function __construct(private OutputInterface $output, private string $command)
    {
    }

    public function run(): void
    {
        $this->processFile(__DIR__ . '/../tests/compatible-types.md');
        $this->processFile(__DIR__ . '/../tests/incompatible-types.md');
    }

    private function processFile(string $file): void
    {
        $handle = fopen($file, 'rb');
        $passes = 0;
        $failures = 0;

        while (true) {
            $line = fgets($handle);

            if ($line === false) {
                break;
            }

            $line = trim($line);

            if ($line === '' || str_starts_with($line, '> ')) {
                continue;
            }

            if (str_starts_with($line, '# ')) {
                $this->output->writeln(substr($line, 2));
                continue;
            }

            if (str_starts_with($line, '- ')) {
                preg_match('/^- `(.+?)` (.+) `(.+)`$/', $line, $matches);
                $super = $matches[1];
                $expected = $matches[2] === 'accepts' ? 'true' : 'false';
                $sub = $matches[3];
                $process = new Process(
                    explode(' ', $this->command),
                    null,
                    null,
                    "compatibility\n" . $super . "\n" . $sub
                );
                $process->run();
                $output = $process->getOutput();
                $caseText = substr($line, 2);

                if (!$process->isSuccessful()) {
                    if ($output === '') {
                        $this->output->writeln(sprintf('<error>"%s" failed with no output</error>', $caseText));
                    } else {
                        if ($this->output->isVerbose()) {
                            $this->output->writeln(sprintf('<error>"%s" failed:</error>', $caseText));
                            $this->output->writeln($output);
                        } else {
                            $this->output->writeln(sprintf('<error>"%s" failed</error>', $caseText));
                        }
                    }

                    $failures++;
                    continue;
                }

                if ($output === $expected) {
                    $this->output->writeln(sprintf('<info>%s</info>', $caseText));
                    $passes++;
                } else {
                    $this->output->writeln(
                        sprintf('<error>"%s" failed. Expected "%s", got "%s"</error>', $caseText, $expected, $output)
                    );
                    $failures++;
                }
            }
        }

        $this->output->writeln(sprintf('<info>%d passed</info>', $passes));
        $this->output->writeln(sprintf('<error>%d failed</error>', $failures));

        fclose($handle);
    }
}
