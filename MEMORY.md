# Agent memory (MEMORY.md)

## Project identity
- **Name:** NigerShop
- **Type:** Marketplace / e-commerce platform
- **Target:** Nigerien small merchants and buyers
- **Current State:** in developement
- **Stack:** PHP, HTML, CSS, JavaScript, PDO, MySQL/MariaDB, Bootstrap 5

## Current architecture
- `public/` : front facing pages
- `src/Core/` : infrastructure classes
- `src/Controllers/` : business logic and action control
- `src/Models/` : datab- `src/Hellpers/` : reusable utilities
- `src/Middleware/` : auth/admin protection
- `includes/` : shared view components
- `assets/` : styles, JavaScript, images, uploads

##  Evolution history
### Completed steps
- Database schema created in `database/nigershop.sql`
- Light MVC structure put in place with `src/Controllers`, `src/Models`, `src/Core`
- Authentication built with pages in `public/auth/*.php`
- A first base of unit tests present in `tests/`
- Database class refined with static init(), isConnected(), and comprehensive PHPDoc
- Comprehensive unit tests for Database class methods with fixtures in setUp()
- Validator class refined and its unit tests documented with PHPDoc
- Router test class improved and comprehensively documented with PHPDoc

### Notable refactors
- Consolidated DB access with PDO in `src/Core/Database.php`
- Centralized validation in `src/Core/Validator.php`
- Clear separation between controllers and views
- Renamed and cleaned variables and methods for consistency

## Important decisions
- Always consider the context of project before proposing a solution.
- Do not restart from scratch if an architecture or decision has already been validated.
- For database access use PDO and prepared statements
- Keep presentation logic in views, business logic in controllers
- Reuse includes for header/footer/messages
- When a solution affects UX, keep the interface simple, clear, and suitable for a real project.
- Avoid duplicated code and large monolithic files.
- Do not store temporary details that have no long-term value.
- Always keep the "Next step" section up to date so work can resume quickly.
- Prefer centralized validation and helpers
- Keep code readable and evolution-friendly

## Current challenges
- Link routes and controllers reliably
- Cover key models with unit tests
- Secure uploads, sessions and form input
- Standardize naming and file organization
- Add better error and feedback handling

## Next milestones
1. ✓ Make unit tests for the Database class methods — **COMPLETED**
2. ✓ Review Validator class methods and its unit tests — **COMPLETED**
3. ✓ Review Router class methods and create unit tests — **COMPLETED**
4. Conception of `src/Core/Logger.php` to log user/admin/dev actions and create unit tests
5. Start conception of `src/Models/*.php` files

## Update format
After each session, update only the useful sections with this format:
- Date: YYYY-MM-DD
- Project: ...
- Completed task: ...
- Decision made: ...
- Files involved: ...
- Next step: ...

## Light log
- 2026-07-25 NigerShop : initial memory file created
- 2026-07-25 NigerShop : **Database & DatabaseTest refinement session completed**
  - **Completed:** Refined `src/Core/Database.php` with static init(), isConnected(), PHPDoc
  - **Completed:** Created comprehensive unit tests in `tests/DatabaseTest.php` with fixtures
  - **Tests added:** testIsConnected, testQuery, testFetch, testFetchReturnsNull, testFetchAll, testFetchColumn, testLastInsertId, testQueryThrowsExceptionWithInvalidSQL
  - **Key improvements:** Added setUp() with test fixtures, proper assertions, edge case tests
  - **Git commit:** "Refine Database class and its unit tests"
- 2026-07-27 NigerShop : **Validator refactor and test documentation session completed**
  - **Completed:** Refine `src/Core/Validator.php` and `tests/ValidatorTest.php` unit tests with fixtures
  - **Completed:** Added PHPDoc comments to `tests/ValidatorTest.php` and `src/Core/Validator.php`
  - **Git commit:** "Refine Validator class and its unit tests"
- 2026-07-31 NigerShop : **Router refactor, test improvement and documentation session completed**
  - **Completed:** Refine `src/Core/Router.php` and creation of `tests/RouterTest.php` unit tests
  - **Completed:** Corrected two broken test methods (testRoutingWithExistingFile and testRoutingWithNonExistingFile)
  - **Completed:** Added comprehensive PHPDoc comments to all test methods
  - **Key improvements:** 
    - Fixed testRoutingWithExistingFile() to properly test existing route matching
    - Fixed testRoutingWithNonExistingFile() to properly test non-existing route exception handling
    - Added detailed PHPDoc documentation explaining what each test verifies
    - Preserved all existing working tests while making only necessary corrections
  - **Git commit:** "Refine Validator class and its unit tests"

## What to remember after each session
- The completed task.
- The current bug, if any.
- The next concrete action.
- The validated technical decisions.
- The modified files or modules.
- Newly confirmed user preferences.

## Work preferences
- Prefer concrete examples in PHP, SQL, CSS, Bootstrap, and basic JavaScript.
- Explain the reasoning behind technical choices, not just the final code.
- Favor best practices: validation, security, clean architecture, reusable code.

## Notes
- Build each feature through controller methods
- Reuse existing infrastructure where possible