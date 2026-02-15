# Contributing to Samuel Mencke Portfolio

First off, thank you for considering contributing to this portfolio project! It's people like you that make open source great.

## Code of Conduct

This project and everyone participating in it is governed by our commitment to:
- Being respectful and inclusive
- Welcoming newcomers
- Focusing on constructive feedback
- Maintaining a harassment-free environment

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to see if the problem has already been reported. When you create a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** (screenshots, code snippets)
- **Describe the behavior you observed** and what behavior you expected
- **Include system details** (OS, browser, PHP version)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a step-by-step description** of the suggested enhancement
- **Provide specific examples** to demonstrate the enhancement
- **Explain why this enhancement would be useful**

### Pull Requests

1. Fork the repository
2. Create a new branch from `main`:
   ```bash
   git checkout -b feature/my-feature
   # or
   git checkout -b fix/my-bugfix
   ```
3. Make your changes
4. Write or update tests if needed
5. Update documentation
6. Commit your changes:
   ```bash
   git commit -m "Add feature: description"
   ```
7. Push to your fork:
   ```bash
   git push origin feature/my-feature
   ```
8. Open a Pull Request

## Development Setup

### Prerequisites

- PHP 8.1+
- Composer
- Node.js (for asset building)
- Git

### Installation

```bash
# Clone your fork
git clone https://github.com/your-username/portfolio.git
cd portfolio

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env with your settings
nano .env

# Start development server
php -S localhost:8000
```

### Project Structure

```
├── api/              # API endpoints
├── assets/           # CSS, JS, images
├── config/           # Configuration files
├── partials/         # PHP partials
├── .env             # Environment variables (not in git)
├── index.php        # Main entry point
└── README.md        # Documentation
```

## Style Guidelines

### PHP Code Style

Follow PSR-12 coding standards:

```php
<?php

declare(strict_types=1);

namespace App;

class Example
{
    public function doSomething(string $param): array
    {
        // Code here
        return [];
    }
}
```

### JavaScript Code Style

- Use ES6+ features
- 4 spaces for indentation
- Semicolons required
- Single quotes for strings

```javascript
const myFunction = (param) => {
    const result = doSomething(param);
    return result;
};
```

### CSS Style

- Use CSS variables for colors
- BEM methodology for class names
- 4 spaces for indentation

```css
.project-card {
    background: var(--card-bg);
}

.project-card__title {
    font-size: 1.5rem;
}

.project-card--featured {
    border: 2px solid var(--accent);
}
```

## Testing

### Manual Testing Checklist

Before submitting a PR, please test:

- [ ] Site loads without errors
- [ ] All sections are visible
- [ ] Projects load from GitHub API
- [ ] Project modals open and display correctly
- [ ] Dark/Light mode toggle works
- [ ] Contact form validates input
- [ ] Responsive design works on mobile
- [ ] No console errors in browser

### PHP Testing

```bash
# Check PHP syntax
php -l index.php
php -l github_api.php

# Run PHP CodeSniffer (if installed)
phpcs --standard=PSR12 *.php
```

## Documentation

- Update README.md if you change functionality
- Add JSDoc comments to JavaScript functions
- Comment complex PHP logic
- Update CHANGELOG.md for notable changes

## Commit Messages

Use conventional commit format:

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting, semicolons, etc)
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `chore:` Build process or auxiliary tool changes

Examples:
```
feat: add image carousel to project modal
fix: resolve API rate limiting issue
docs: update installation instructions
```

## Questions?

Feel free to open an issue with your question or contact:
- Email: contact@samuel-mencke.com
- GitHub Issues: [https://github.com/Samuel-Mencke/portfolio/issues](https://github.com/Samuel-Mencke/portfolio/issues)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing! 🎉
