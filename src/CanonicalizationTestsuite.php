<?php

namespace Midnight\PhpTypesystemTests;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function array_key_exists;
use function explode;
use function fclose;
use function fgets;
use function fopen;
use function preg_match;
use function sprintf;
use function str_starts_with;
use function substr;

final class CanonicalizationTestsuite
{
    public function __construct(private OutputInterface $output, private string $command)
    {
    }

    public function run(): void
    {
        $handle = fopen(__DIR__ . '/../tests/canonicalize.md', 'rb');
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
                preg_match('/^- `(.+?)`( -> `(.+)`)?$/', $line, $matches);

                if (array_key_exists(3, $matches)) {
                    $from = $matches[1];
                    $to = $matches[3];
                } else {
                    $from = $matches[1];
                    $to = $matches[1];
                }

                $process = new Process(explode(' ', $this->command), null, null, $from);
                $process->run();
                $output = $process->getOutput();

                if (!$process->isSuccessful()) {
                    if ($output === '') {
                        $this->output->writeln(sprintf('<error>"%s" failed with no output</error>', $from));
                    } else {
                        if ($this->output->isVerbose()) {
                            $this->output->writeln(sprintf('<error>"%s" failed:</error>', $from));
                            $this->output->writeln($output);
                        } else {
                            $this->output->writeln(sprintf('<error>"%s" failed</error>', $from));
                        }
                    }

                    $failures++;
                    continue;
                }

                if ($output === $to) {
                    $this->output->writeln(sprintf('<info>%s</info>', substr($line, 2)));
                    $passes++;
                } else {
                    $this->output->writeln(
                        sprintf('<error>"%s" failed. Expected "%s", got "%s"</error>', $from, $to, $output)
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
