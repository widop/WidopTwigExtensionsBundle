# Widop Twig Extensions

This bundle provides additional TWIG functionnalities:
* date_interval
* truncate_at

## date_interval examples:
This TWIG method/filter act as an inversed countdown.

```twig
 {{ date('-2days') | date_interval }} --> 2 days ago
 {{ date_interval('now') }} <==> {{ date_interval() }} --> A few moments ago
 {{ date_interval(date('-1years')) }} --> A year ago
```

## truncate_at examples:
This TWIG method truncates a string at a specific offset or before the current
word so that the given limit is never exceeded.
``twig
 {{ truncate_at('ab c', 1, false) }}      --> ''
 {{ truncate_at('ab c', 1, true) }}       --> 'a'
 {{ truncate_at('ab c', 2, false) }}      --> 'ab'
 {{ truncate_at('ab c', 3, false) }}      --> 'ab'
 {{ truncate_at('ab c', 4, false) }}      --> 'ab c'
 {{ truncate_at('ab c', 5, false) }}      --> 'ab c'
 {{ truncate_at('ab      c', 5, false) }} --> 'ab'
 {{ truncate_at('       c', 1, false) }}  --> 'c' // string is trimmed!
 {{ truncate_at('       c', 2, false) }}  --> 'c'
 {{ truncate_at('       c', 5, false) }}  --> 'c'
 {{ truncate_at('abcde', 5, false) }}     --> 'abcde'
 {{ truncate_at('abcde ', 5, false) }}    --> 'abcde'
 {{ truncate_at('abcde ', 6, false) }}    --> 'abcde'
```
