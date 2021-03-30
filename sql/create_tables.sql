
CREATE TABLE musician (
    name VARCHAR(42) NOT NULL,
    passwordHash TEXT NOT NULL,
    email VARCHAR(100),
    latestCommit TIMESTAMP(3),
    age INTEGER,

    PRIMARY KEY (name)
);

CREATE TABLE project (
    name VARCHAR(42) NOT NULL,
    updatedAt TIMESTAMP(3) NOT NULL,
    createdAt TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    authorName VARCHAR(42) NOT NULL,
    mainBranchName VARCHAR(42) NOT NULL,
    private BOOLEAN NOT NULL,

    PRIMARY KEY (authorName,name)
);

CREATE TABLE branch (
    name VARCHAR(42) NOT NULL,
    updatedAt TIMESTAMP(3) NOT NULL,
    authorName VARCHAR(42) NOT NULL,
    projectName VARCHAR(42) NOT NULL,

    PRIMARY KEY (authorName,projectName,name)
);

CREATE TABLE version (
    id TEXT NOT NULL,
    createdAt TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    authorName VARCHAR(42) NOT NULL,
    projectName VARCHAR(42) NOT NULL,
    branchName VARCHAR(42) NOT NULL,

    PRIMARY KEY (authorName,projectName,branchName,id)
);

CREATE TABLE partition (
    name VARCHAR(42) NOT NULL,
    content TEXT NOT NULL,
    authorName VARCHAR(42) NOT NULL,
    projectName VARCHAR(42) NOT NULL,
    branchName VARCHAR(42) NOT NULL,
    versionID TEXT NOT NULL,

    PRIMARY KEY (authorName,projectName,branchName,versionID)
);

CREATE TABLE contributor (
    contributorName VARCHAR(42) NOT NULL,
    authorName VARCHAR(42) NOT NULL,
    projectName VARCHAR(42) NOT NULL,

    PRIMARY KEY (authorName,projectName,contributorName)
);

CREATE TABLE friend (
    followerName VARCHAR(42) NOT NULL,
    followingName VARCHAR(42) NOT NULL,

    PRIMARY KEY (followerName,followingName)
);

CREATE UNIQUE INDEX musician_email_unique ON musician(email);

ALTER TABLE project ADD FOREIGN KEY (authorName) REFERENCES musician(name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE project ADD FOREIGN KEY (authorName, name, mainBranchName) REFERENCES branch(authorName, projectName, name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE branch ADD FOREIGN KEY (authorName, projectName) REFERENCES project(authorName, name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE version ADD FOREIGN KEY (authorName, projectName, branchName) REFERENCES branch(authorName, projectName, name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE partition ADD FOREIGN KEY (authorName, projectName, branchName, versionID) REFERENCES version(authorName, projectName, branchName, id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE contributor ADD FOREIGN KEY (contributorName) REFERENCES musician(name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE contributor ADD FOREIGN KEY (authorName, projectName) REFERENCES project(authorName, name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friend ADD FOREIGN KEY (followerName) REFERENCES musician(name) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE friend ADD FOREIGN KEY (followingName) REFERENCES musician(name) ON DELETE CASCADE ON UPDATE CASCADE;

GRANT ALL ON ALL TABLES IN SCHEMA public TO "tpphp";
