-- ============================================================
-- AI Tool Recommendation Portal — Database Schema v2
-- Safe to re-run: uses IF NOT EXISTS + INSERT IGNORE
-- ============================================================

CREATE DATABASE IF NOT EXISTS `ai_tool_portal`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `ai_tool_portal`;

-- ── Users Table ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('student','admin') DEFAULT 'student',
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: Admin@1234)
INSERT IGNORE INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('PortalAdmin', 'admin@silveroakuni.ac.in',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ── Categories Table ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL,
  `description`   TEXT,
  `slug`          VARCHAR(50) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `categories` (`id`, `category_name`, `slug`, `description`) VALUES
(1, 'Code Assistant',    'code',    'AI tools for coding, debugging and code review'),
(2, 'Image Generation',  'image',   'Text-to-image and creative AI tools'),
(3, 'Data Analysis',     'data',    'Data cleaning, visualization and insights tools'),
(4, 'Writing & Content', 'writing', 'Copywriting, SEO and long-form content tools'),
(5, 'Audio & Video',     'audio',   'Transcription, voice cloning and video editing tools'),
(6, 'AI Agents',         'agent',   'Autonomous agent frameworks and platforms');

-- ── AI Tools Table ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ai_tools` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `tool_name`   VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `url`         VARCHAR(255),
  `pricing`     ENUM('Free','Freemium','Paid') DEFAULT 'Freemium',
  `category_id` INT,
  `rating`      DECIMAL(3,1) DEFAULT 4.5,
  `icon`        VARCHAR(10)  DEFAULT '🤖',
  `added_by`    INT,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`added_by`)    REFERENCES `users`(`id`)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add rating & icon columns if upgrading from v1 schema
ALTER TABLE `ai_tools` ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,1) DEFAULT 4.5;
ALTER TABLE `ai_tools` ADD COLUMN IF NOT EXISTS `icon`   VARCHAR(10)  DEFAULT '🤖';
ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `slug` VARCHAR(50)  DEFAULT 'general';

-- Seed tools
INSERT IGNORE INTO `ai_tools` (`id`,`tool_name`,`description`,`url`,`category_id`,`pricing`,`rating`,`icon`,`added_by`) VALUES
(1, 'DevMind AI',       'An advanced coding companion that writes, debugs, and optimizes code across 40+ languages.',       'https://devmind.ai',    1, 'Freemium', 4.8, '🤖', 1),
(2, 'Visionary Studio', 'Create hyper-realistic images from text prompts using state-of-the-art diffusion models.',         'https://visionary.ai',  2, 'Paid',     5.0, '🎨', 1),
(3, 'DataSense Pro',    'Automatically clean, analyze, and visualize datasets in seconds with natural language queries.',   'https://datasense.ai',  3, 'Free',     4.5, '📊', 1),
(4, 'WordForge AI',     'Generate SEO articles, ad copy, and email sequences that match your brand voice perfectly.',       'https://wordforge.ai',  4, 'Freemium', 4.6, '✍️', 1),
(5, 'SoundMind',        'AI-powered transcription, voice cloning and podcast editing with studio-quality output.',          'https://soundmind.ai',  5, 'Freemium', 4.9, '🎵', 1),
(6, 'AgentFlow',        'Build and deploy autonomous AI agents that browse, reason and execute multi-step tasks 24/7.',     'https://agentflow.ai',  6, 'Paid',     4.3, '⚡', 1),
(7, 'PixelCraft AI',    'Transform sketches into photorealistic renders with AI artistic style transfer.',                  'https://pixelcraft.ai', 2, 'Paid',     4.7, '🖼️', 1),
(8, 'CodeReview Bot',   'Automated code review catching bugs, security issues and anti-patterns before production.',        'https://codebot.ai',    1, 'Freemium', 4.9, '🔍', 1),
(9, 'NexusAgent',       'Multi-modal AI agent with persistent memory, tool use, and real-time web access.',                 'https://nexusagent.ai', 6, 'Paid',     4.6, '🌐', 1);

-- ── Reviews Table ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT,
  `tool_id`     INT,
  `rating`      INT CHECK (`rating` >= 1 AND `rating` <= 5),
  `review_text` TEXT,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)     ON DELETE CASCADE,
  FOREIGN KEY (`tool_id`) REFERENCES `ai_tools`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;