# Basic types
- `string` accepts `string`
- `int` accepts `int`
- `float` accepts `float`
- `bool` accepts `bool`
- `mixed` accepts `string`
- `mixed` accepts `int`
- `mixed` accepts `float`
- `mixed` accepts `bool`
- `bool` accepts `true`
- `bool` accepts `false`
- `true` accepts `true`
- `false` accepts `false`
- `mixed` accepts `object`
- `object` accepts `object`
- `iterable` accepts `array`
- `object` accepts `Foo`
- `(int)` accepts `int`
- `int` accepts `(int)`
- `string` accepts `non-empty-string`
- `non-empty-string` accepts `'a'`
- `non-empty-string` accepts `non-empty-string`

# Unions
- `string|int` accepts `string`
- `string|int` accepts `int`
- `string|float|int` accepts `float`
- `string|mixed` accepts `int`
- `string|int|bool` accepts `string|int`
- `bool|int|string` accepts `string|int`
- `string|null` accepts `string`
- `string|null` accepts `null`
- `string|float` accepts `string|int`

# Callables
- `callable(string): void` accepts `callable(string|int): void`
- `callable(): string|int` accepts `callable(): string`
- `callable(): void` accepts `callable(): string`
- `callable` accepts `callable(): mixed`
- `callable(): void` accepts `callable`
- `callable` accepts `callable(): int`
- `callable(): (int|string)` accepts `callable(): string`
- `callable(float): void` accepts `callable(): void`

# Tuples
- `array{string, int}` accepts `array{string, int}`
- `array{string}` accepts `array{string, int}`
- `array{string, int|bool}` accepts `array{string, int}`

# Structs
- `array{foo: string}` accepts `array{foo: string}`
- `array{foo: string|int}` accepts `array{foo: string}`
- `array{foo: string, bar: int}` accepts `array{foo: string, bar: int}`
- `array{foo?: string}` accepts `array{foo: string}`
- `array{foo?: string, bar: int}` accepts `array{bar: int}`
- `array{foo: string}` accepts `array{foo: string, bar: int}`
- `array{bar: int, foo: string}` accepts `array{foo: string, bar: int}`
- `array{foo?: string}` accepts `array{foo?: string}`

# String Literals
- `"test"` accepts `"test"`
- `'test'` accepts `'test'`
- `"test"` accepts `'test'`
- `'test'` accepts `"test"`
- `string` accepts `"test"`
- `string` accepts `'test'`
- `mixed` accepts `'test'`
- `mixed` accepts `"test"`

# Int Literals
- `0` accepts `0`
- `int` accepts `0`
- `1` accepts `1`
- `int` accepts `1`
- `69` accepts `69`
- `int` accepts `69`
- `-1` accepts `-1`
- `int` accepts `-1`
- `-69` accepts `-69`
- `int` accepts `-69`

# Collections
- `list<int>` accepts `list<int>`
- `array<int, string>` accepts `list<string>`
- `iterable<string>` accepts `array<string>`
- `array<string>` accepts `array<array-key, string>`
- `array` accepts `array<array-key, mixed>`
- `array<float>` accepts `array<string, float>`
- `iterable<string|int>` accepts `iterable<string>`
- `iterable<bool>` accepts `list<bool>`
- `array<string, string>` accepts `array{foo: string}`
- `array<string, string>` accepts `array{foo: string, bar: string}`
- `array<string, string|int>` accepts `array{foo: string, bar: int}`
- `list<string>` accepts `array{"foo", "bar"}`
- `list<string>` accepts `array{string, string}`
- `list<string|int>` accepts `array{int, string}`
- `array<int, string>` accepts `array{string, string}`
- `list` accepts `list<mixed>`

# Parens
- `(callable(): string)|string` accepts `string`
- `(callable(): string)|string` accepts `callable(): string`
