<?php

namespace Midnight\PhpTypeSystemTests;

use DirectoryIterator;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Throwable;

use function array_key_exists;
use function array_key_last;
use function array_shift;
use function basename;
use function count;
use function explode;
use function floor;
use function fopen;
use function sprintf;
use function str_starts_with;
use function stream_get_line;
use function strlen;
use function substr;

final class Runner
{
    private const COMMENT_PREFIX = '> ';
    private const HEADER_PREFIX = '# ';
    private const TEST_PREFIX = '- ';

    /** @var array<string, array<string, array<string, bool>>> */
    private array $results = [];
    /** @var list<array{string, callable}> */
    private array $testTypes;

    public function __construct(private string $command)
    {
        $this->testTypes = [
            ['/^`([^`]+?)`$/', $this->testCanonicalizesToItself(...)],
            ['/^`([^`]+?)` -> `([^`]+?)`$/', $this->testCanonicalizesTo(...)],
            ['/^`(.+?)` accepts `(.+?)`$/', $this->testAccepts(...)],
            ['/^`(.+?)` doesn\'t accept `(.+?)`$/', $this->testNotAccepts(...)],
        ];
    }

    private static function assert(bool $result, string $message): void
    {
        if ($result) {
            return;
        }

        throw new RuntimeException($message);
    }

    private static function isComment(string $line): bool
    {
        return str_starts_with($line, self::COMMENT_PREFIX);
    }

    private static function isHeader(string $line): bool
    {
        return str_starts_with($line, self::HEADER_PREFIX);
    }

    private static function isBlank(string $line): bool
    {
        return $line === '';
    }

    private static function isTest(string $line): bool
    {
        return str_starts_with($line, self::TEST_PREFIX);
    }

    public function run(OutputInterface $output): void
    {
        $iterator = new DirectoryIterator(__DIR__ . '/../tests/');
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            $this->runFile($file->getRealPath(), $output);
        }
        $successes = 0;
        $total = 0;
        foreach ($this->results as $sections) {
            foreach ($sections as $tests) {
                foreach ($tests as $result) {
                    $total++;
                    if ($result) {
                        $successes++;
                    }
                }
            }
        }
        $output->writeln('');
        $output->writeln(sprintf('%d/%d successful (%d%%)', $successes, $total, floor($successes / $total * 100)));
    }

    private function runFile(string $file, OutputInterface $output): void
    {
        $this->results[$file] = [];
        $output->writeln('');
        $output->writeln(sprintf('<info>%s</info>', basename($file)));
        $handle = fopen($file, 'rb');
        while (true) {
            $line = stream_get_line($handle, 1024, "\n");
            if ($line === false) {
                break;
            }
            $this->handleLine($line, $output);
        }
    }

    private function handleLine(string $line, OutputInterface $output): void
    {
        if (self::isBlank($line) || self::isComment($line)) {
            return;
        }
        if (self::isHeader($line)) {
            $this->endSection($output);
            $header = substr($line, strlen(self::HEADER_PREFIX));
            $this->startSection($header, $output);
            return;
        }
        if (self::isTest($line)) {
            $test = substr($line, strlen(self::TEST_PREFIX));
            $this->runTest($test, $output);
            return;
        }
        throw new RuntimeException('Unknown line type: ' . $line);
    }

    private function startSection(string $name, OutputInterface $output): void
    {
        $currentFile = $this->currentFile();
        if (array_key_exists($name, $this->results[$currentFile])) {
            throw new RuntimeException(sprintf('Duplicate section %s in file %s', $name, $currentFile));
        }
        $this->results[$currentFile][$name] = [];
        $output->writeln('');
        $output->writeln('<comment>-- ' . $name . ' --</comment>');
    }

    private function endSection(OutputInterface $output): void
    {
        if ($output->isVerbose()) {
            return;
        }
        $sectionName = $this->currentSection();
        if ($sectionName === null) {
            return;
        }
        $allSuccessful = true;
        $lastSectionResults = $this->results[$this->currentFile()][$sectionName];
        foreach ($lastSectionResults as $result) {
            if ($result) {
                continue;
            }
            $allSuccessful = false;
        }
        if (!$allSuccessful) {
            return;
        }
        $output->writeln(sprintf('<info>✓ All %d tests were successful</info>', count($lastSectionResults)));
    }

    private function runTest(string $test, OutputInterface $output): void
    {
        foreach ($this->testTypes as $testType) {
            $result = preg_match($testType[0], $test, $matches);
            if ($result !== 1) {
                continue;
            }
            array_shift($matches);
            try {
                $testType[1](...$matches);
                $success = true;
                if ($output->isVerbose()) {
                    $output->writeln('<info>✓ ' . $test . '</info>');
                }
            } catch (Throwable $e) {
                $success = false;
                $output->writeln('<error>✗ ' . $e->getMessage() . '</error>');
            }
            $this->results[$this->currentFile()][$this->currentSection()][$test] = $success;
            return;
        }
        throw new RuntimeException('Unknown test: ' . $test);
    }

    private function testCanonicalizesToItself(string $type): void
    {
        $output = $this->callAdapter("canonicalize\n$type");

        self::assert($output === $type, sprintf('Expected "%s" to canonicalize to itself, got "%s"', $type, $output));
    }

    private function testCanonicalizesTo(string $from, string $to): void
    {
        $output = $this->callAdapter("canonicalize\n$from");

        self::assert($output === $to, sprintf('Expected "%s" to canonicalize to "%s", got "%s"', $from, $to, $output));
    }

    private function testAccepts(string $super, string $sub): void
    {
        $output = $this->callAdapter("compatibility\n$super\n$sub");

        self::assert($output === 'true', sprintf('Expected "%s" to accept "%s", but it doesn\'t', $super, $sub));
    }

    private function testNotAccepts(string $super, string $sub): void
    {
        $output = $this->callAdapter("compatibility\n$super\n$sub");

        self::assert($output === 'false', sprintf('Expected "%s" to not accept "%s", but it does', $super, $sub));
    }

    private function callAdapter(string $input): string
    {
        $process = new Process(explode(' ', $this->command), null, null, $input);
        $process->run();
        $output = $process->getOutput();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to run');
        }
        return $output;
    }

    private function currentFile(): string|null
    {
        return array_key_last($this->results);
    }

    private function currentSection(): string|null
    {
        return array_key_last($this->results[$this->currentFile()]);
    }
}
