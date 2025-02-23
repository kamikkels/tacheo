# Tacheo

Tacheo is a PHP library created to evaluate working days within a given time period.
It provides utility functions to simplify handling working days within give timesets.

## Usage

Example usage:
```
$tacheo = new Tacheo('1940-01-01 0:00:00', '2050-01-08 03:14:08');

$tacheo->workingDaysBetween( ['NSW, Australia', 'VIC, Australia'] )
```

## Contributing

Contributions are welcome! Follow these steps to contribute:

1. Fork the repository.

2. Create a new branch (git checkout -b feature-branch).

3. Make your changes and commit (git commit -m "Add new feature").

4. Push the branch (git push origin feature-branch).

5. Open a pull request.

## Coding Guidelines

Follow PSR-12 coding standards.

Use meaningful commit messages.

Write unit tests for new features.

## License

This project is licensed under the MIT License.

---

Feel free to update or add more features!