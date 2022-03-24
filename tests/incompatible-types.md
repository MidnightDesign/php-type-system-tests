# Simple
- `int` doesn't accept `string`
- `bool` doesn't accept `mixed`
- `int` doesn't accept `string|int`
- `true` doesn't accept `false`
- `true` doesn't accept `bool`
- `false` doesn't accept `true`
- `false` doesn't accept `bool`
- `object` doesn't accept `mixed`
- `string` doesn't accept `string|int`
- `non-empty-string` doesn't accept `string`
- `non-empty-string` doesn't accept `''`

# Union
- `int|string` doesn't accept `string|int|bool`
- `string|float` doesn't accept `bool`

# Callable
- `callable(): string` doesn't accept `callable(): int`
- `callable(float): void` doesn't accept `callable(string): void`
- `callable(): void` doesn't accept `string`

# Tuple
- `array{string, float}` doesn't accept `array{string}`
- `array{string|int, float}` doesn't accept `array{float, float}`
- `array{int, string}` doesn't accept `int`

# Struct
- `array{foo: int}` doesn't accept `array{foo: string}`
- `array{foo: string, bar: int}` doesn't accept `array{foo: string}`
- `array{foo: string}` doesn't accept `array{foo?: string}`
- `array{foo: string}` doesn't accept `bool`
- `array{foo?: string, bar: int}` doesn't accept `array{bar: string}`

# Intersection
- `array{foo: string}&array{bar: int}` doesn't accept `bool`

# Literal
- `'test'` doesn't accept `string`
- `"test"` doesn't accept `string`
- `"bar"` doesn't accept `"foo"`
- `27` doesn't accept `23`
- `27` doesn't accept `int`

# Collections
- `array<string, string>` doesn't accept `list<string>`
- `array` doesn't accept `iterable`
- `iterable` doesn't accept `string`
- `iterable<string, int>` doesn't accept `iterable<int, string>`
- `array` doesn't accept `float`
- `iterable<string, int>` doesn't accept `iterable<string, float>`
- `iterable<string, int>` doesn't accept `iterable<bool, int>`
- `list` doesn't accept `float`
- `array<string, string>` doesn't accept `array{foo: int}`
