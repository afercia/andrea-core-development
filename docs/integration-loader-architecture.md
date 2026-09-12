# Integration Loader Architecture

This document describes how the AFCD plugin's integration loader works, how it
relates to the platform's (Yoast.com) DI-based approach, and the options for
evolving it in the future.

---

## Current Implementation (Option A: Runtime `glob()` Discovery)

The `AFCD_Plugin` class auto-discovers and initializes all integrations at
runtime on every request.

### Flow

1. `afcd-core-development.php` → `new AFCD_Plugin()`
2. `AFCD_Plugin::load_integrations()` → `glob()` all `class-afcd-*.php` files in `includes/`
3. `require_once` each discovered file
4. `AFCD_Plugin::discover_integrations()` → `get_declared_classes()` filtered by `AFCD_Integration_Interface`
5. For each found class: `new $class()` → `$integration->register_hooks()`

### How to add a new integration

Drop a new `class-afcd-*.php` file in `includes/` that implements
`AFCD_Integration_Interface`. No changes to `AFCD_Plugin` needed.

### Trade-offs

| Aspect | Detail |
|---|---|
| **Pros** | Zero config. Drop a file, it just works. No build step. |
| **Cons** | `glob()` + `require_once` + `get_declared_classes()` runs on every request. |
| **Scale** | Negligible cost at 3-5 integrations. Becomes noticeable at 20+. |

---

## Platform Approach (Yoast.com Core)

The platform uses **Symfony Dependency Injection** with a **pre-compiled container**.
No filesystem scanning happens at runtime.

### Flow

1. **Build time** (via `composer` / `wp-env`): Symfony's DI component compiles
   `Dependency_Injection/container_config.php` into a generated
   `Dependency_Injection/Generated/Cached_Container.php` (~5,500 lines).
   This file contains a hardcoded map of every service — class name, constructor
   args, and dependencies. No `glob()`, no `get_declared_classes()`.

2. **Runtime**: The `Loader` reads a **pre-built array** of integration classnames.
   For each classname, it calls `$this->container->get( $classname )` — a simple
   array lookup in the compiled container (zero filesystem I/O). Then
   `$integration->register_hooks()`.

3. **Precondition grouping**: Integrations are grouped by their precondition key
   so that preconditions are checked **once per group**, not once per integration.
   This reduces condition evaluations from O(n) to O(g) where g = number of unique
   precondition combinations.

### Key files

| File | Role |
|---|---|
| `Dependency_Injection/container_config.php` | Defines which directories/classes to register, aliases, service providers. |
| `Dependency_Injection/Generated/Cached_Container.php` | Auto-generated compiled container. Committed to the repo. |
| `Loader.php` | Reads pre-built integration classname arrays, calls `$container->get()` + `register_hooks()`. |
| `Integrations/Integration_Interface.php` | Interface: `register_hooks()` + `get_preconditions()`. |
| `Preconditions/Conditions_Checker.php` | Evaluates preconditions, caches results via `Conditions_Cache`. |

### How to add a new integration (platform)

Create a class implementing `Integration_Interface`, place it in the correct
directory. Run the build step to regenerate `Cached_Container.php`. No manual
registration needed — Symfony's `registerClasses()` + `symfony/finder` handles
discovery at build time.

### Trade-offs

| Aspect | Detail |
|---|---|
| **Pros** | Zero runtime filesystem scanning. Precondition grouping. Full DI with autowiring. |
| **Cons** | Requires Symfony DI + `symfony/finder` as dependencies. Requires a build step. More complex. |
| **Scale** | Designed for 100+ integrations across multiple domains. |

---

## Option B: Generated Static Class List (Future)

A middle ground between the current approach and the platform's full DI container.
No Symfony dependency, but no runtime `glob()` either.

### Flow

1. A one-time script (or `composer` task) scans `includes/` for files implementing
   `AFCD_Integration_Interface` and writes a generated file:

   ```php
   // includes/afcd-integrations.php (generated, committed)
   return [
       'AFCD_Admin_Page',
       'AFCD_Debug_Spinner',
       'AFCD_Mailpit',
   ];
   ```

2. `AFCD_Plugin` loads this file:

   ```php
   $integrations = require __DIR__ . '/afcd-integrations.php';
   foreach ( $integrations as $class_name ) {
       $integration = new $class_name();
       $integration->register_hooks();
   }
   ```

### Trade-offs

| Aspect | Detail |
|---|---|
| **Pros** | Zero `glob()`, zero `get_declared_classes()` at runtime. Simple. No Symfony. |
| **Cons** | Requires a generation step (script or composer task). Must remember to regenerate when adding/removing integrations. |
| **Scale** | Good for 10-30 integrations. |

---

## Decision Log

| Date | Decision | Rationale |
|---|---|---|
| 2026-02-10 | Keep Option A (runtime `glob()`) | 3 integrations. Cost is negligible. Simplest approach. No build step. |
| — | Evaluate Option B when integrations reach ~10+ | Eliminates runtime filesystem scanning without adding Symfony. |
| — | Evaluate full Symfony DI if the plugin grows to 20+ integrations or needs autowiring | Matches the platform's approach. Adds precondition grouping and service decoration. |

---

## Comparison Summary

| | Option A (Current) | Option B (Static List) | Platform (Symfony DI) |
|---|---|---|---|
| **Runtime filesystem scan** | Yes (`glob` + `get_declared_classes`) | No | No |
| **Build step required** | No | Yes (generate class list) | Yes (compile container) |
| **External dependencies** | None | None | `symfony/dependency-injection`, `symfony/finder` |
| **Auto-discovery** | Runtime | Build time | Build time |
| **Precondition grouping** | No | No | Yes |
| **Service autowiring** | No | No | Yes |
| **Complexity** | Low | Low-Medium | High |
| **Best for** | 1-10 integrations | 10-30 integrations | 30+ integrations |
