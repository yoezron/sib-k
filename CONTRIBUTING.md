# 🤝 Contributing to SIB-K

Terima kasih atas minat Anda untuk berkontribusi pada Sistem Informasi Bimbingan dan Konseling (SIB-K)!

## 📋 Development Roadmap

Proyek ini dikembangkan dalam 8 fase:

### ✅ FASE 1: SETUP & FONDASI - COMPLETED
- Setup CodeIgniter 4
- Database migrations
- Authentication system
- Base models and controllers

### 🚧 FASE 2: MODUL ADMIN - IN PROGRESS
- Admin dashboard with statistics
- User management CRUD
- Student management with Excel import/export
- Class management
- Academic year management

### 📅 FASE 3-8: Upcoming
- Konseling module
- Assessment module
- Student & Parent portals
- Communication features
- Reporting system
- Finalization & testing

## 🔧 Development Setup

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Composer
- Git

### Local Development

1. Fork the repository
2. Clone your fork:
```bash
git clone https://github.com/YOUR_USERNAME/sib-k.git
cd sib-k
```

3. Install dependencies:
```bash
composer install
```

4. Setup database and run migrations (see INSTALLATION.md)

5. Create a new branch for your feature:
```bash
git checkout -b feature/your-feature-name
```

## 📝 Coding Standards

### PHP Code Style
- Follow PSR-12 coding standard
- Use meaningful variable and function names
- Add PHPDoc comments for classes and methods
- Keep functions small and focused

Example:
```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * User Management Controller
 * Handles CRUD operations for users
 */
class UserController extends BaseController
{
    /**
     * Display list of users
     * 
     * @return string
     */
    public function index()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll();
        
        return view('admin/users/index', $data);
    }
}
```

### Database Migrations
- Always create migrations for database changes
- Use descriptive migration names
- Include both up() and down() methods
- Test migrations before committing

```php
php spark make:migration CreateTableName
```

### Models
- Use CodeIgniter's Model class
- Define validation rules
- Use proper relationships
- Add helper methods when needed

### Views
- Use Bootstrap 5.3 components
- Keep views clean and focused
- Use escaping for all user input: `<?= esc($variable) ?>`
- Follow existing template structure

### Security
- Always validate and sanitize user input
- Use CSRF protection
- Use parameterized queries (Query Builder)
- Hash passwords with password_hash()
- Use esc() helper for XSS prevention

## 🔄 Git Workflow

### Branch Naming
- Feature: `feature/feature-name`
- Bugfix: `bugfix/bug-description`
- Hotfix: `hotfix/critical-fix`

### Commit Messages
Follow conventional commits format:

```
type(scope): subject

body (optional)

footer (optional)
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

Examples:
```
feat(admin): add user management CRUD

Implements full CRUD operations for user management including:
- List users with pagination
- Create new user
- Edit existing user
- Delete user
- Role assignment

Closes #123
```

```
fix(auth): resolve login redirect issue

Fixed issue where users were not redirected to correct dashboard
after login based on their role.
```

### Pull Request Process

1. Update your branch with latest main:
```bash
git checkout main
git pull origin main
git checkout your-branch
git rebase main
```

2. Run tests and linting:
```bash
composer test
composer lint
```

3. Push your changes:
```bash
git push origin your-branch
```

4. Create Pull Request:
   - Use descriptive title
   - Reference related issues
   - Include screenshots for UI changes
   - Describe what was changed and why
   - List any breaking changes

5. Wait for review and address feedback

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php spark test

# Run specific test
php spark test --filter UserModelTest
```

### Writing Tests
- Write tests for new features
- Test both success and failure cases
- Use descriptive test names

Example:
```php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UserModel;

class UserModelTest extends CIUnitTestCase
{
    public function testCanCreateUser()
    {
        $userModel = new UserModel();
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'full_name' => 'Test User',
            'role_id' => 1
        ];
        
        $result = $userModel->insert($data);
        $this->assertIsNumeric($result);
    }
}
```

## 📚 Documentation

- Update README.md for major features
- Add inline comments for complex logic
- Update INSTALLATION.md if setup changes
- Create/update API documentation

## 🐛 Reporting Bugs

### Before Submitting
- Check if the bug is already reported
- Reproduce the bug in latest version
- Collect error messages and logs

### Bug Report Template
```markdown
**Describe the bug**
A clear description of what the bug is.

**To Reproduce**
Steps to reproduce:
1. Go to '...'
2. Click on '...'
3. See error

**Expected behavior**
What you expected to happen.

**Screenshots**
If applicable, add screenshots.

**Environment:**
- PHP Version: 
- MySQL Version:
- Browser: 
- OS: 

**Additional context**
Any other context about the problem.
```

## 💡 Feature Requests

- Check existing feature requests first
- Explain the use case
- Describe expected behavior
- Consider implementation complexity

## 📋 Development Priorities

### High Priority (FASE 2)
- [ ] User management CRUD
- [ ] Student management with Excel import
- [ ] Class management
- [ ] Dashboard statistics

### Medium Priority (FASE 3-5)
- [ ] Counseling session management
- [ ] Assessment system
- [ ] Student/Parent portals

### Low Priority (FASE 6-8)
- [ ] Advanced reporting
- [ ] Mobile app API
- [ ] Performance optimization

## 🙏 Recognition

Contributors will be added to:
- README.md Contributors section
- CHANGELOG.md for their contributions
- Release notes

## 📞 Contact

- GitHub Issues: For bugs and features
- Email: yoezron@github.com
- Project Discussion: Use GitHub Discussions

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to SIB-K! 🎉
