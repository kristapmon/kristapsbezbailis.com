#Changelog
All notable changes to this project will be documented in changelog.md file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

#Version Numbering
This project uses a three-number versioning system (X.Y.Z):

X (Major): Breaking changes, major feature overhauls

Y (Minor): New features, significant improvements

Z (Patch): Bug fixes, minor improvements

Example: Version 1.2.3

1: Major version

2: Minor version

3: Patch version

When to increment:

Major (X): When making incompatible changes that might break existing functionality

Minor (Y): When adding functionality in a backward-compatible manner

Patch (Z): When making backward-compatible bug fixes

#Making Changelog Entries
#For New Changes in Development:
Add changes under the [Unreleased] section

Categorize them under appropriate headers:

Added for new features

Changed for changes in existing functionality

Deprecated for soon-to-be removed features

Removed for removed features

Fixed for bug fixes

Security for vulnerability fixes

Technical for technical changes/dependencies

Keep entries concise but descriptive

#When Releasing a Version:
Convert the [Unreleased] section to a version number with date (e.g., [1.0.0] - 2024-01-20)

Create a new empty [Unreleased] section at the top

#General Rules:
Newest changes always go at the top of the file

Each version should be in descending order (newest to oldest)

Group related changes under the same category

Use bullet points for each entry

#Development Workflow:
For Every Code Change:

ALWAYS add an entry to the [Unreleased] section in this changelog

Write clear, descriptive change notes

Categorize changes appropriately using the headers above

Commit changes with meaningful commit messages

For Version Releases:

Move [Unreleased] changes to a new version section with today's date

Update version number in ProjectSettings.asset (bundleVersion)

Create a git tag for the version

Create a new empty [Unreleased] section at the top

#Release Process:
When asked to make a release, follow these steps:

Review Changes:

Review all changes under [Unreleased]

Ensure all changes are properly categorized

Verify all changes are documented

Choose Version Number:

For new features: increment minor version (0.1.0 → 0.2.0)

For bug fixes: increment patch version (0.1.0 → 0.1.1)

For breaking changes: increment major version (0.1.0 → 1.0.0)

Update Files:

Move [Unreleased] changes to new version section with today's date

Update version in ProjectSettings.asset (bundleVersion)

Create new empty [Unreleased] section

Commit and Tag:

Commit all changes with message "release: Version X.Y.Z"

Create a git tag for the version (e.g., v0.2.0)