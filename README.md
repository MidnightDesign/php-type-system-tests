# midnight/php-type-system-tests

This project defines a test suite for the de-facto standard docblock type system
established by [PHPStan](https://github.com/phpstan/phpstan),
[Psalm](https://github.com/vimeo/psalm) and
[PhpStorm](https://www.jetbrains.com/phpstorm/) with the goal of ironing out the
subtle differences between them.

## Running the tests

You can run the test suite by calling `./app run "<adapter command>"`
where `<adapter command>` is a command that takes a specific input (which you
can learn more about by taking a look at the included adapters in the
[`adapters`](adapters) folder).

To run one of the included adapters:

- `./app run "adapters/phpstan"`
- `./app run "adapters/psalm"`
- `./app run "adapters/php-types"`[^php-types]

On Windows, that would be `php app run "php adapters/phpstan"`, etc. Or you just
use WSL.

You can also point it to your own adapter that accepts the same kind of input.

By default, it only shows failing cases, but you can output results for all
cases with the `-v` flag.  

## The goal

I think it's awesome that the developers of all the static analyzers we use
every day are working together to come up with an inter-compatible type syntax.
However, there are still a ton of subtle differences between them.

In a perfect world, there would be a single canonical implementation of the type
system that's used by all static analyzers instead of them all implementing the
same thing and trying to make them compatible.

In our world, a common test suite is the next best thing.

Non-goal: This package is not intended to test static analysis capabilities.
Just the type system.

## Please help

I'm looking for your help, specifically:

- The [test cases](tests) are written in Markdown to make them incredibly easy
  to read _and_ write. If you come up with a new case, just open up a quick pull
  request.
- Many test cases can be argued about, for example: Should the parts of a union
  be sorted alphabetically? Should `array-key` be normalized to `string|int` or
  is `array-key` more readable? Should the canonical version of `iterable<T>` be
  explicit (`iterable<mixed, T>`) or implicit (`iterable<T>`)? Please open up an
  issue to start the discussion.
- If this description of the project is confusing, hard to read or missing
  important detail, please open up a pull request. I'm not a good writer and my
  first language isn't English.
- Like most programmers, I'm bad at naming things, as witnessed by the name of
  this very project. If you come up with a clever name for it, I have no problem
  at all with changing it at this point. 

[^php-types]: [`adapters/php-types`](https://github.com/MidnightDesign/php-types)
  is my own little implementation of the type system. It's (deliberately) way
  stricter and opinionated than PHPStan, Psalm and PhpStorm - for example, it
  only accepts the explicit `array<K, V>` syntax, but not the implicit `T[]`
  (which actually means `array<array-key, T>`) syntax.

