CREATE TABLE IF NOT EXISTS public.mavenanalyticsdata
(
    "CUSTOMER_ID" character varying COLLATE pg_catalog."default",
    "EVENT" character varying COLLATE pg_catalog."default",
    "OFFER_ID" character varying COLLATE pg_catalog."default",
    "TIME" integer,
    "IS_AMOUNT" integer,
    "IS_REWARD" integer,
    "IS_OTHER" integer,
    "REWARD_AMOUNT" integer,
    "AMOUNT_AMOUNT" numeric(7,2)
)