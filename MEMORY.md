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
- `src/Models/` : database entities
- `src/Hellpers/` : reusable utilities
- `src/Middleware/` : auth/admin protection
- `includes/` : shared view components
- `assets/` : styles, JavaScript, images, uploads

##  Evolution history
### Completed steps
- Database schema created in `database/nigershop.sql`
- Light MVC structure put in place with `src/Controllers`, `src/Models`, `src/Core`
- `src/Core` infrastructre is completed and comprehensively documented with PHPDoc
- Unit tests present in `tests/`

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
4. ✓ Conception of `src/Core/Logger.php` to log user/admin/dev actions and create unit tests — **COMPLETED**
5. Start conception of `src/Models/*.php` files
    - Designing User model and its unit test — **COMPLETED**
    - Designing Category model and its unit test — **COMPLETED**
    - Designing Annonce model and its unit test — **COMPLETED**
    - Designing Favorite model and its unit test
    - Designing Message model and its unit test

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
- 2026-07-25 NigerShop : **Database & DatabaseTest classes refinement session completed**
  - **Completed:** Refined `src/Core/Database.php` with static init(), isConnected(), PHPDoc
  - **Completed:** Created comprehensive unit tests in `tests/DatabaseTest.php` with fixtures
  - **Tests added:** testIsConnected, testQuery, testFetch, testFetchReturnsNull, testFetchAll, testFetchColumn, testLastInsertId, testQueryThrowsExceptionWithInvalidSQL
  - **Key improvements:** Added setUp() with test fixtures, proper assertions, edge case tests
  - **Git commit:** "Refine Database class and its unit tests"
- 2026-07-27 NigerShop : **Validator class refactor and test documentation session completed**
  - **Completed:** Refine `src/Core/Validator.php` and `tests/ValidatorTest.php` unit tests with fixtures
  - **Completed:** Added PHPDoc comments to `tests/ValidatorTest.php` and `src/Core/Validator.php`
  - **Git commit:** "Refine Validator class and its unit tests"
- 2026-07-31 NigerShop : **Router class refactor, test improvement and documentation session completed**
  - **Completed:** Refine `src/Core/Router.php` and creation of `tests/RouterTest.php` unit tests
  - **Completed:** Corrected two broken test methods (testRoutingWithExistingFile and testRoutingWithNonExistingFile)
  - **Completed:** Added comprehensive PHPDoc comments to all test methods
  - **Key improvements:** 
    - Fixed testRoutingWithExistingFile() to properly test existing route matching
    - Fixed testRoutingWithNonExistingFile() to properly test non-existing route exception handling
    - Added detailed PHPDoc documentation explaining what each test verifies
    - Preserved all existing working tests while making only necessary corrections
  - **Git commit:** "Refine Validator class and its unit tests"
- 2026-08-02 NigerShop : **Logger & LoggerTest classes creation and documentation session completed**
  - **Completed:** Conception of Logger class and creation of `tests/LoggerTest.php` unit tests
  - **Completed:** Added comprehensive PHPDoc comments to all test methods
  - **Git commit:** Conception of Logger and creation of its unit tests
  - **Next step:** Conception of `src/Models` files
- 2026-08-04 NigerShop : **Designing User.php model**
  - **Completed:** Handling data comming from the users tables with apropriete properties
  - **Completed:** CRUD operations designed
  - **Completed:** Validation of user informations before adding to the database
  - **Git commit:** Models/Use - v1.0
- 2026-08-10 NigerShop : **Fixed User.php model – corrected static property usage and typo**
  - **Completed:** Changed all property accesses from self::$property to $this->property
  - **Completed:** Verified that CRUD operations, validation, and password hashing work correctly
  - **Git commit:** [fix] : variable instanciation call
  - **Next step:** Write unit tests for User model (tests/UserTest.php)
- 2026-08-21 NigerShop : **Fixed UserTest.php - corrected tests design**
  - **Completed:** Refactor all test fonctions for better verification
  - **Git commit:** [update] : refactor `UserTest.php` tests
  - **Next step:** Design Category model and its unit tests
- 2026-08-23 NigerShop : **Category model and unit tests completed**
  - **Completed:** Handling data comming from the categories table with apropriete properties
  - **Completed:** CRUD operations designed
  - **Completed:** Validation of categories datas before adding to the database
  - **Completed:** All methods tests pass correctly
  - **First git commit:** Models/Category - v1.0
  - **Second git commit:** Category model unit tests
  - **Next step:** Design Annonce model and its unit tests
- 2026-08-28 NigerShop : **Annonce model and unit tests completed**
  - **Completed:** Covered Annonce searches by ID, category, owner and slug
  - **Completed:** Added valid and invalid tests for create, update and delete operations
  - **Completed:** Verified returned Annonce objects and database persistence after update and delete
  - **First git commit:** Models/Annonce - v1.0
  - **Second git commit:** Annonce model unit tests
  - **Next step:** Design Favorite model and its unit tests

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