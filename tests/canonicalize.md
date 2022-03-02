> These are the test cases for checking whether outputting a parsed type results
> in the original input. In some cases, the input type can be simplified, so it
> will be different from the original input. These cases use the A -> B
> notation.

# Simple

- `string`
- `int`
- `float`
- `bool`
- `true`
- `false`
- `(int)` -> `int`
- `((int))` -> `int`

# Unions

- `string|int`
- `string|int|float`
- `list<string>|list<int>`
- `list<string|int>`
- `string|string` -> `string`
- `string|string|string` -> `string`
- `string|'a'` -> `string`
- `'a'|string` -> `string`
- `'a'|string|'b'|string` -> `string`
- `mixed|string|non-empty-string|'a'` -> `mixed`
- `'a'|non-empty-string|string|mixed` -> `mixed`
- `string|list<int>|array<array-key, int|string>|'foo'` -> `string|array<string|int, int|string>`
- `list<string|int>|list<int|string>` -> `list<string|int>`

# Intersections

- `Foo&Bar`

# Generics

- `list<string>`
- `array<string, bool>`
- `array<string, array<string, int>>`

# Callable

- `callable` -> `callable(): mixed`
- `callable(): void`
- `callable(string): void`
- `callable(string): int`
- `callable(string, bool): float`
- `callable(list<int>, string, array<int, bool>): string`
- `callable(): (string|int)`
- `callable(): (array{foo: string}&array{bar: int})`

# Tuples

- `array{string, int}`
- `array{array{int, bool}, int}`

# Structs

- `array{foo: string}`
- `array{foo: string, bar: int}`
- `array{optional?: float}`

# Literals

- `'test'`
- `"test"` -> `'test'`
- `0`
- `1`
- `69`
- `-1`
- `-23`
- `99999999`

# Aliases

- `array-key` -> `string|int`

# Collections

- `array` -> `array<string|int, mixed>`
- `array<bool>` -> `array<string|int, bool>`
- `array<string, int>`
- `list` -> `list<mixed>`
- `list<float>`
- `iterable` -> `iterable<mixed, mixed>`
- `iterable<string>` -> `iterable<mixed, string>`
- `iterable<string, int>`
- `Foo`
