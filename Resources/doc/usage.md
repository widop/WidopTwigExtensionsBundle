# Usage

## Date Interval

This twig method/filter acts as an inversed countdown.

``` twig
{{ date('-2days') | date_interval }} --> 2 days ago

{{ date_interval('now') }}           --> A few moments ago

{{ date_interval(date('-1years')) }} --> A year ago
```

## Truncate At

This twig method/filter truncates a string at a specific offset or before the current word so that
the given limit is never exceeded.

``` twig
{{ 'ab cd' | truncate_at(3) }}     --> 'ab'
{{ truncate_at('ab cd', 3) }}      --> 'ab'

{{ truncate_at('ab c', 1) }}       --> ''
{{ truncate_at('ab c', 1, true) }} --> 'a'

{{ truncate_at('ab c', 2) }}       --> 'ab'
{{ truncate_at('ab c', 3) }}       --> 'ab'
{{ truncate_at('ab c', 4) }}       --> 'ab c'
{{ truncate_at('ab c', 5) }}       --> 'ab c'

{{ truncate_at('ab      c', 5) }}  --> 'ab'
{{ truncate_at('       c', 1) }}   --> 'c' // string is trimmed!
{{ truncate_at('       c', 2) }}   --> 'c' // string is trimmed!
{{ truncate_at('       c', 5) }}   --> 'c' // string is trimmed!

{{ truncate_at('abcde', 5)  }}     --> 'abcde'
{{ truncate_at('abcde ', 5) }}     --> 'abcde'
{{ truncate_at('abcde ', 6) }}     --> 'abcde'
```
