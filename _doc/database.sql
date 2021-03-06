PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

-- Major * 1,000,000 + Minor * 1,000 + Revision
PRAGMA user_version = 2000000;

-- Drop tables
DROP TABLE IF EXISTS "session_data";
DROP TABLE IF EXISTS "sessions";
DROP TABLE IF EXISTS "user_settings";
DROP TABLE IF EXISTS "global_settings";
DROP TABLE IF EXISTS "shares_in_groups";
DROP TABLE IF EXISTS "users_in_groups";
DROP TABLE IF EXISTS "shares";
DROP TABLE IF EXISTS "groups";
DROP TABLE IF EXISTS "users";

-- Create tables

-- Users
CREATE TABLE "users"(
	"id" INTEGER PRIMARY KEY AUTOINCREMENT,
	"name" TEXT NOT NULL UNIQUE,
	"password" BLOB NOT NULL,
	"enabled" INTEGER NOT NULL DEFAULT 1,
	"type" INTEGER NOT NULL DEFAULT 1,
	"comment" TEXT NOT NULL DEFAULT ''
);

-- Groups
CREATE TABLE "groups"(
	"id" INTEGER PRIMARY KEY AUTOINCREMENT,
	"name" TEXT NOT NULL UNIQUE,
	"enabled" INTEGER NOT NULL DEFAULT 1,
	"comment" TEXT NOT NULL DEFAULT ''
);

-- Shares
CREATE TABLE "shares"(
	"id" INTEGER PRIMARY KEY AUTOINCREMENT,
	"name" TEXT NOT NULL UNIQUE,
	"path" BLOB NOT NULL,
	"enabled" INTEGER NOT NULL DEFAULT 1,
	"comment" TEXT NOT NULL DEFAULT ''
);

-- User <-> share group mappings
CREATE TABLE "users_in_groups"(
	"user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,
	"group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,
	UNIQUE("user_id", "group_id") ON CONFLICT REPLACE
);

-- Share group <-> share mappings
CREATE TABLE "shares_in_groups"(
	"group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,
	"share_id" INTEGER NOT NULL REFERENCES "shares"("id") ON UPDATE CASCADE ON DELETE CASCADE,
	"writable" INTEGER NOT NULL DEFAULT 0,
	UNIQUE("group_id", "share_id") ON CONFLICT REPLACE
);

-- Global settings
CREATE TABLE "global_settings"(
	"key" TEXT NOT NULL UNIQUE ON CONFLICT REPLACE,
	"value" BLOB DEFAULT NULL
);

-- User settings
CREATE TABLE "user_settings"(
	"key" TEXT NOT NULL,
	"user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,
	"value" BLOB DEFAULT NULL,
	UNIQUE("user_id", "key") ON CONFLICT REPLACE
);

-- Sessions
CREATE TABLE "sessions"(
	"session_id" BLOB NOT NULL,
	"timestamp" INTEGER NOT NULL DEFAULT (STRFTIME('%s', 'now')),
	UNIQUE("session_id")
);
CREATE TABLE "session_data"(
	"session_id" BLOB NOT NULL REFERENCES "sessions"("session_id") ON UPDATE CASCADE ON DELETE CASCADE,
	"key" TEXT NOT NULL,
	"value" BLOB DEFAULT NULL,
	UNIQUE("session_id", "key") ON CONFLICT REPLACE
);

COMMIT TRANSACTION;
