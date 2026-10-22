# AGENTS.md

Gameoscope is a handmade collection of video game screenshots, built as a static
site. Symfony renders the pages, Stenope crawls them into `build/`, and the
result is rsynced to the server. There is no database: games and screenshots are
files on disk. The site's content is in French.

Check `composer.json` and `package.json` for exact versions (Symfony 8.1, PHP 8.5,
Webpack Encore 7). The build needs the Node versions declared in `engines`.

## How it fits together

- `games/` is a separate git repository (`make games` clones it), ignored here.
  Each game is a folder named after its slug, with `info.json` (name, release
  `year`, publication `date`, `studio`, `draft`) and its screenshots. Never
  commit or rewrite anything in `games/` unless asked.
- `tom32i/showcase-bundle` (configured in `config/packages/tom32i_showcase.yaml`)
  browses `games/`, maps folders to `App\Model\Game` and images to
  `App\Model\Screenshot`, and serves resized images through Glide presets at
  `/image/{preset}/{path}`. Screenshot flags such as `spoil` are stored in the
  image files' own properties through Imagick, which is why `ext-imagick` is
  required.
- `AppController` renders the public pages: game list, game page, about page.
  Draft games are hidden in `prod` only.
- `AdminController` (`/admin`) edits games and screenshots. It only exists
  outside `prod` and is excluded from the Stenope build. `make admin` runs it in
  dev with `APP_DEBUG=0`.
- `stenope/stenope` turns the prod app into static HTML: `bin/console stenope:build`
  writes `build/`, including every image preset.

## Commands

- `make start` (Symfony server on :8000) and `make watch` (Encore) for development.
- `make game` creates a new game interactively. It prompts, so don't run it in a
  non-interactive shell. Write `games/<slug>/info.json` yourself instead.
- `make lint` runs PHP CS Fixer, PHPStan (level 8 with strict rules, on `bin/`,
  `config/`, `public/` and `src/`), the Twig and YAML lints, ESLint and
  stylelint. PHP CS Fixer, ESLint and stylelint fix what they can in place.
  Run it before committing.
- `bin/console lint:container` checks service wiring.
- There is no test suite. Verify changes by rendering the pages, or with a build.

## Building

`make build` compiles assets, generates the image cache, runs `stenope:build`
and optimizes the images with optimage. A full build takes over an hour and
writes about 13 GB to `build/` (plus the Glide cache in `var/cache/games`), so:

- Don't start a full build casually. Check free disk space first, and run long
  builds in the background.
- To check a change, render the affected pages with `make start` instead.
- `make deploy@*` rsyncs `build/` to a server. Only run it when explicitly asked.

## Code conventions

- Follow the existing code: constructor property promotion, `#[Route]` and
  `#[AsCommand]` attributes, autowired services, thin controllers.
- Import `Route` from `Symfony\Component\Routing\Attribute` (the `Annotation`
  namespace no longer exists in Symfony 8).
- PHP CS Fixer uses the `@Symfony` ruleset plus the project rules in
  `.php-cs-fixer.dist.php` (strict types, `concat_space: one`, no Yoda style).
- PHPStan rules forbid short ternaries (`?:`) and non-boolean conditions. Don't
  silence errors with `@phpstan-ignore` or baseline entries, fix the cause.
- Stylesheets use Sass modules (`sass:color`, `sass:string`) rather than the
  global functions, which stylelint forbids.
- Install Symfony packages with `composer require` and let the Flex recipes
  generate their config. `config/reference.php` is generated: don't edit or
  commit it.

## Related repositories

The bundles live in `~/Sites/opensource/` (`ShowcaseBundle`, `Stenope`,
`optimage`) and are maintained by the same author. When a fix belongs in one of
them, change it there and release it, rather than patching around it here.
