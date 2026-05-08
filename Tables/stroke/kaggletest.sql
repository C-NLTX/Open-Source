CREATE TABLE IF NOT EXISTS public.kaggletest
(
    id bigint,
    gender character varying COLLATE pg_catalog."default",
    age double precision,
    hypertension integer,
    heart_disease integer,
    ever_married character varying COLLATE pg_catalog."default",
    work_type character varying COLLATE pg_catalog."default",
    residence_type character varying COLLATE pg_catalog."default",
    avg_glucose_level double precision,
    bmi double precision,
    smoking_status character varying COLLATE pg_catalog."default",
    stroke integer,
    prediction double precision
)