--
-- Populate users table
--
INSERT INTO
fuma_new.users(id,name,email,password,remember_token,created_at,updated_at)
SELECT id,name,email,password,remember_token,created_at,updated_at FROM
fuma_new_tmp.users;
-- --------------------------------------------------------

--
-- Populate SubmitJobs table
--
INSERT INTO
fuma_new.SubmitJobs(jobID, old_id, email, title, created_at, updated_at, status, user_id, type)
SELECT sj.jobID, sj.jobID, sj.email as job_mail, sj.title, sj.created_at, sj.updated_at, sj.status, u.id as user_id, 'snp2gene'
FROM fuma_new_tmp.SubmitJobs sj
JOIN fuma_new_tmp.users u ON sj.email = u.email
	AND u.id IS NOT NULL;
-- --------------------------------------------------------

--
-- Populate SubmitJobs table based on JobMonitor table
--
UPDATE fuma_new.SubmitJobs AS s
JOIN fuma_new_tmp.JobMonitor AS t ON s.old_id = t.jobID
SET s.started_at = t.started_at, s.completed_at = t.completed_at;
-- --------------------------------------------------------

--
-- Populate SubmitJobs table adding public jobs based on PublicResults.jobID (there is match)
--
UPDATE fuma_new.SubmitJobs
JOIN fuma_new_tmp.PublicResults
ON fuma_new.SubmitJobs.old_id = fuma_new_tmp.PublicResults.jobID
SET 
fuma_new.SubmitJobs.is_public			= 1,
fuma_new.SubmitJobs.old_id 				= fuma_new_tmp.PublicResults.id,
fuma_new.SubmitJobs.author 				= fuma_new_tmp.PublicResults.author,
fuma_new.SubmitJobs.publication_email 	= fuma_new_tmp.PublicResults.email,
fuma_new.SubmitJobs.phenotype 			= fuma_new_tmp.PublicResults.phenotype,
fuma_new.SubmitJobs.publication 		= fuma_new_tmp.PublicResults.publication,
fuma_new.SubmitJobs.sumstats_link 		= fuma_new_tmp.PublicResults.sumstats_link,
fuma_new.SubmitJobs.sumstats_ref 		= fuma_new_tmp.PublicResults.sumstats_ref,
fuma_new.SubmitJobs.notes 				= fuma_new_tmp.PublicResults.notes,
fuma_new.SubmitJobs.published_at 		= fuma_new_tmp.PublicResults.created_at
WHERE fuma_new.SubmitJobs.type = 'snp2gene';
-- --------------------------------------------------------

--
-- Populate SubmitJobs table adding public jobs when there is no match neither based on id
-- nor on email (completely unknown jobs)
-- assigne these jobs to the user with user_id = 2
--
INSERT INTO
fuma_new.SubmitJobs(
    jobID,
    old_id,
    user_id,
    email, 
    title, 
    created_at, 
    updated_at, 
    status, 
    type,
    is_public, 
    author, 
    publication_email, 
    phenotype, 
    publication, 
    sumstats_link, 
    sumstats_ref, 
    notes, 
    published_at)

SELECT 
fuma_new_tmp.PublicResults.jobID                         AS jobID,
fuma_new_tmp.PublicResults.id                            AS old_id,
2                                                        AS user_id,
(SELECT email FROM fuma_new.users WHERE id = 2)          AS email,
fuma_new_tmp.PublicResults.title                         AS title,
fuma_new_tmp.PublicResults.created_at                    AS created_at,
fuma_new_tmp.PublicResults.update_at                     AS updated_at,
'OK'                                                     AS status,
'snp2gene'                                               AS type,
1                                                        AS is_public,
fuma_new_tmp.PublicResults.author                        AS author,
fuma_new_tmp.PublicResults.email                         AS publication_email,
fuma_new_tmp.PublicResults.phenotype                     AS phenotype,
fuma_new_tmp.PublicResults.publication                   AS publication,
fuma_new_tmp.PublicResults.sumstats_link                 AS sumstats_link,
fuma_new_tmp.PublicResults.sumstats_ref                  AS sumstats_ref,
fuma_new_tmp.PublicResults.notes                         AS notes,
fuma_new_tmp.PublicResults.created_at                    AS published_at
FROM fuma_new_tmp.PublicResults 
LEFT JOIN fuma_new.SubmitJobs ON fuma_new_tmp.PublicResults.jobID = fuma_new.SubmitJobs.jobID
LEFT JOIN fuma_new_tmp.users ON fuma_new_tmp.PublicResults.email = fuma_new_tmp.users.email
WHERE fuma_new.SubmitJobs.type is null
AND fuma_new_tmp.users.email is null;
-- --------------------------------------------------------

--
-- Populate SubmitJobs table adding public jobs when there is no match based on id
-- but there is a match based on email
--
INSERT INTO
fuma_new.SubmitJobs(
    jobID,
    old_id, 
    email, 
    title, 
    created_at, 
    updated_at, 
    status, 
    user_id, 
    type,
    is_public, 
    author, 
    publication_email, 
    phenotype, 
    publication, 
    sumstats_link, 
    sumstats_ref, 
    notes, 
    published_at)

SELECT 
fuma_new_tmp.PublicResults.jobID                         AS jobID,
fuma_new_tmp.PublicResults.id                            AS old_id,
fuma_new_tmp.PublicResults.email                         AS email,
fuma_new_tmp.PublicResults.title                         AS title,
fuma_new_tmp.PublicResults.created_at                    AS created_at,
fuma_new_tmp.PublicResults.update_at                     AS updated_at,
'OK'                                                     AS status,
(SELECT id FROM fuma_new_tmp.users WHERE email = fuma_new_tmp.PublicResults.email)  AS user_id,
'snp2gene'                                               AS type,
1                                                        AS is_public,
fuma_new_tmp.PublicResults.author                        AS author,
fuma_new_tmp.PublicResults.email                         AS publication_email,
fuma_new_tmp.PublicResults.phenotype                     AS phenotype,
fuma_new_tmp.PublicResults.publication                   AS publication,
fuma_new_tmp.PublicResults.sumstats_link                 AS sumstats_link,
fuma_new_tmp.PublicResults.sumstats_ref                  AS sumstats_ref,
fuma_new_tmp.PublicResults.notes                         AS notes,
fuma_new_tmp.PublicResults.created_at                    AS published_at

FROM fuma_new_tmp.PublicResults 
LEFT JOIN fuma_new.SubmitJobs ON fuma_new_tmp.PublicResults.jobID = fuma_new.SubmitJobs.jobID 
LEFT JOIN fuma_new_tmp.users ON fuma_new_tmp.PublicResults.email = fuma_new_tmp.users.email
WHERE fuma_new.SubmitJobs.type is null
AND fuma_new_tmp.users.email is not null;
-- --------------------------------------------------------

--
-- Populate SubmitJobs table adding gene2func jobs
--
INSERT INTO fuma_new.SubmitJobs (old_id, email, title, created_at, updated_at, status, user_id, type, parent_id)
SELECT
    g2f.jobID,
    g2f.email,
    g2f.title,
    g2f.created_at,
    g2f.created_at,
    'OK',
    u.id,
    'gene2func',
    sj.jobID
FROM
    fuma_new_tmp.gene2func g2f
LEFT JOIN fuma_new_tmp.users u ON g2f.email = u.email
LEFT JOIN fuma_new.SubmitJobs sj ON g2f.snp2gene = sj.jobID AND sj.type = 'snp2gene';
-- --------------------------------------------------------

--
-- Insert gene2func jobs on SubmitJobs table based on the g2f_jobID of Public results (whene there is no match)
-- these jobs (gene2func) are child jobs of public snp2gene jobs that have been deleted by the users
--
INSERT INTO
fuma_new.SubmitJobs(
    old_id, 
    email, 
    title, 
    created_at, 
    updated_at, 
    status, 
    user_id, 
    type,
    parent_id,
    author, 
    publication_email, 
    phenotype, 
    publication, 
    sumstats_link, 
    sumstats_ref, 
    notes, 
    published_at)

SELECT 
    fuma_new_tmp.PublicResults.g2f_jobID                                                AS old_id,
    fuma_new_tmp.PublicResults.email                                                    AS email,
    fuma_new_tmp.PublicResults.title                                                    AS title,
    fuma_new_tmp.PublicResults.created_at                                               AS created_at,
    fuma_new_tmp.PublicResults.update_at                                                AS updated_at,
    'OK'                                                                                AS status,
    (SELECT id FROM fuma_new_tmp.users WHERE email = fuma_new_tmp.PublicResults.email)  AS user_id,
    'gene2func'                                                                         AS type,
    fuma_new_tmp.PublicResults.jobID                                                    AS parent_id,
    fuma_new_tmp.PublicResults.author                                                   AS author,
    fuma_new_tmp.PublicResults.email                                                    AS publication_email,
    fuma_new_tmp.PublicResults.phenotype                                                AS phenotype,
    fuma_new_tmp.PublicResults.publication                                              AS publication,
    fuma_new_tmp.PublicResults.sumstats_link                                            AS sumstats_link,
    fuma_new_tmp.PublicResults.sumstats_ref                                             AS sumstats_ref,
    fuma_new_tmp.PublicResults.notes                                                    AS notes,
    fuma_new_tmp.PublicResults.created_at                                               AS published_at
FROM SubmitJobs 
RIGHT JOIN fuma_new_tmp.PublicResults ON SubmitJobs.old_id = fuma_new_tmp.PublicResults.g2f_jobID AND SubmitJobs.type = 'gene2func'
WHERE fuma_new_tmp.PublicResults.g2f_jobID != 0
AND SubmitJobs.old_id is null;


-- -- only those that do not exist (there is no match), this should return public gene2func jobs that have been deleted by the users
-- SELECT 
--     SubmitJobs.jobID, 
--     SubmitJobs.old_id, 
--     SubmitJobs.parent_id, 
--     SubmitJobs.type, 
--     SubmitJobs.is_public, 

--     fuma_new_tmp.PublicResults.*
-- FROM SubmitJobs 
-- RIGHT JOIN fuma_new_tmp.PublicResults ON SubmitJobs.old_id = fuma_new_tmp.PublicResults.g2f_jobID AND SubmitJobs.type = 'gene2func'
-- WHERE fuma_new_tmp.PublicResults.g2f_jobID != 0
-- AND SubmitJobs.old_id is null --comment out for deleted ones, comment in for all in common matched and not matched




-- -- the common ones, this should return all the public gene2func jobs in common between SubmitJobs and PublicResults
-- SELECT 
-- 	SubmitJobs.jobID as S_jobID,
-- 	SubmitJobs.old_id as S_old_id,
--     SubmitJobs.parent_id as S_parent_id,
    
--     PublicResults.jobID as P_jobID,
--     PublicResults.g2f_jobID as P_g2f_jobID
-- FROM SubmitJobs
-- JOIN fuma_new_tmp.PublicResults
-- ON SubmitJobs.old_id = fuma_new_tmp.PublicResults.g2f_jobID
-- WHERE SubmitJobs.type = 'gene2func';


--
-- Populate SubmitJobs table adding celltype jobs
--
INSERT INTO fuma_new.SubmitJobs (old_id, email, title, created_at, updated_at, status, user_id, type, parent_id)
SELECT
    ct.jobID AS old_id,
    ct.email AS email,
    ct.title AS title,
    ct.created_at AS created_at,
    ct.created_at AS updated_at,
    ct.status AS status,
    u.id AS user_id,
    'celltype' AS type,
    sj.jobID AS parent_id
FROM
    fuma_new_tmp.celltype ct
JOIN fuma_new_tmp.users u ON ct.email = u.email
	AND u.email is NOT NULL
LEFT JOIN fuma_new.SubmitJobs sj ON ct.snp2gene = sj.jobID AND sj.type = 'snp2gene';
-- --------------------------------------------------------