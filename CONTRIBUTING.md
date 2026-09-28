# Contributing to LaraCare Account Lockout

Thank you for considering contributing to `lara-care/account-lockout`! We welcome bug reports, feature requests, documentation improvements, and pull requests.

---

## Code of Conduct

Please ensure all interactions in issues, pull requests, and discussions remain respectful, inclusive, and professional.

---

## How to Contribute

### Reporting Bugs
Before creating a bug report, please check existing issues to avoid duplicates. When opening an issue, include:
- PHP version and Laravel framework version
- A clear description of the expected vs. actual behavior
- Minimal reproduction code or steps to trigger the issue

### Requesting Features
Feel free to open an issue describing the feature, why it would be beneficial, and any proposed implementation details.

---

## Development Setup

1. **Fork and clone the repository:**

```bash
   git clone [https://github.com/LaraCare/account-lockout.git](https://github.com/LaraCare/account-lockout.git)
   cd account-lockout
```

2. **Install dependencies:**

```bash
composer install
```


3. **Run tests:**

```bash
vendor/bin/phpunit
```
---

## Pull Request Guidelines

1. **Branch Naming:** Use clear prefixes like `feat/, fix/,` or docs/ (e.g., `feat/route-middleware`).

2. **Code Style:** Adhere to PSR-12 code formatting standards.

3. **Tests:** Ensure all existing tests pass and add new tests covering your changes.

4. **Commit Messages:** Keep commits concise and descriptive (e.g., `fix: resolve race condition in lockout manager`).                                                                                                                                   