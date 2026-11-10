CREATE TABLE users (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(150)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    password        VARCHAR(255)        NOT NULL,
    role            ENUM('client','admin') NOT NULL DEFAULT 'client',
    account_status  ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE categories (
    id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL UNIQUE,
    slug        VARCHAR(120)  NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tags (
    id   BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articles (
    id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    author_id         BIGINT UNSIGNED NOT NULL,
    category_id       BIGINT UNSIGNED NOT NULL,
    title             VARCHAR(255)    NOT NULL,
    slug              VARCHAR(280)    NOT NULL UNIQUE,
    excerpt           VARCHAR(500),
    content           LONGTEXT        NOT NULL,
    thumbnail_path    VARCHAR(255),
    status            ENUM('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED')
                        NOT NULL DEFAULT 'DRAFT',
    revision_note     TEXT,
    rejection_reason  TEXT,
    last_edited_by    BIGINT UNSIGNED,
    view_count        BIGINT UNSIGNED NOT NULL DEFAULT 0,
    submitted_at      DATETIME,
    published_at      DATETIME,
    unpublished_at    DATETIME,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_articles_author   FOREIGN KEY (author_id)      REFERENCES users(id),
    CONSTRAINT fk_articles_category FOREIGN KEY (category_id)    REFERENCES categories(id),
    CONSTRAINT fk_articles_editor   FOREIGN KEY (last_edited_by) REFERENCES users(id),

    INDEX idx_articles_status (status),
    INDEX idx_articles_author (author_id),
    INDEX idx_articles_category (category_id),
    INDEX idx_articles_published_at (published_at),
    FULLTEXT KEY ft_articles_search (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE article_tags (
    article_id BIGINT UNSIGNED NOT NULL,
    tag_id     BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    CONSTRAINT fk_at_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_at_tag     FOREIGN KEY (tag_id)     REFERENCES tags(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE article_images (
    id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    article_id    BIGINT UNSIGNED NOT NULL,
    file_name     VARCHAR(255) NOT NULL,
    file_path     VARCHAR(255) NOT NULL,
    file_size     INT UNSIGNED,
    file_format   VARCHAR(20),
    is_thumbnail  BOOLEAN NOT NULL DEFAULT FALSE,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    INDEX idx_images_article (article_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE article_status_logs (
    id               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    article_id       BIGINT UNSIGNED NOT NULL,
    changed_by       BIGINT UNSIGNED NOT NULL,
    previous_status  ENUM('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED'),
    new_status       ENUM('DRAFT','MENUNGGU_REVIEW','REVISI','DITOLAK','PUBLISHED') NOT NULL,
    note             TEXT,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_logs_user    FOREIGN KEY (changed_by) REFERENCES users(id),
    INDEX idx_logs_article (article_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;