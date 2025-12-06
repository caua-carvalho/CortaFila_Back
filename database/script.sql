-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.appointment_notifications (
  id integer NOT NULL DEFAULT nextval('appointment_notifications_id_seq'::regclass),
  appointment_id integer NOT NULL,
  type character varying NOT NULL CHECK (type::text = ANY (ARRAY['reminder'::character varying, 'confirmation'::character varying, 'custom'::character varying]::text[])),
  sent_at timestamp without time zone NOT NULL,
  CONSTRAINT appointment_notifications_pkey PRIMARY KEY (id),
  CONSTRAINT appointment_notifications_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id)
);
CREATE TABLE public.appointments (
  id integer NOT NULL DEFAULT nextval('appointments_id_seq'::regclass),
  company_id integer NOT NULL,
  client_id integer NOT NULL,
  professional_id integer NOT NULL,
  service_id integer NOT NULL,
  date date NOT NULL,
  time time without time zone NOT NULL,
  status character varying NOT NULL CHECK (status::text = ANY (ARRAY['scheduled'::character varying, 'completed'::character varying, 'cancelled'::character varying]::text[])),
  created_at timestamp without time zone DEFAULT now(),
  CONSTRAINT appointments_pkey PRIMARY KEY (id),
  CONSTRAINT appointments_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id),
  CONSTRAINT appointments_client_id_fkey FOREIGN KEY (client_id) REFERENCES public.clients(id),
  CONSTRAINT appointments_professional_id_fkey FOREIGN KEY (professional_id) REFERENCES public.professionals(id),
  CONSTRAINT appointments_service_id_fkey FOREIGN KEY (service_id) REFERENCES public.services(id)
);
CREATE TABLE public.clients (
  id integer NOT NULL DEFAULT nextval('clients_id_seq'::regclass),
  company_id integer NOT NULL,
  name character varying NOT NULL,
  phone character varying NOT NULL,
  created_at timestamp without time zone DEFAULT now(),
  CONSTRAINT clients_pkey PRIMARY KEY (id),
  CONSTRAINT clients_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);
CREATE TABLE public.companies (
  id integer NOT NULL DEFAULT nextval('companies_id_seq'::regclass),
  name character varying NOT NULL,
  created_at timestamp without time zone DEFAULT now(),
  adress text,
  CONSTRAINT companies_pkey PRIMARY KEY (id)
);
CREATE TABLE public.professional_blocked_times (
  id integer NOT NULL DEFAULT nextval('professional_blocked_times_id_seq'::regclass),
  professional_id integer NOT NULL,
  date date NOT NULL,
  start_time time without time zone NOT NULL,
  end_time time without time zone NOT NULL,
  reason character varying,
  CONSTRAINT professional_blocked_times_pkey PRIMARY KEY (id),
  CONSTRAINT professional_blocked_times_professional_id_fkey FOREIGN KEY (professional_id) REFERENCES public.professionals(id)
);
CREATE TABLE public.professional_services (
  id integer NOT NULL DEFAULT nextval('professional_services_id_seq'::regclass),
  professional_id integer NOT NULL,
  service_id integer NOT NULL,
  duration integer NOT NULL,
  price numeric,
  CONSTRAINT professional_services_pkey PRIMARY KEY (id),
  CONSTRAINT professional_services_professional_id_fkey FOREIGN KEY (professional_id) REFERENCES public.professionals(id),
  CONSTRAINT professional_services_service_id_fkey FOREIGN KEY (service_id) REFERENCES public.services(id)
);
CREATE TABLE public.professional_working_hours (
  id integer NOT NULL DEFAULT nextval('professional_working_hours_id_seq'::regclass),
  professional_id integer NOT NULL,
  day integer NOT NULL CHECK (day >= 0 AND day <= 6),
  start_time time without time zone NOT NULL,
  end_time time without time zone NOT NULL,
  CONSTRAINT professional_working_hours_pkey PRIMARY KEY (id),
  CONSTRAINT professional_working_hours_professional_id_fkey FOREIGN KEY (professional_id) REFERENCES public.professionals(id)
);
CREATE TABLE public.professionals (
  id integer NOT NULL DEFAULT nextval('professionals_id_seq'::regclass),
  company_id integer NOT NULL,
  name character varying NOT NULL,
  phone character varying NOT NULL,
  avatar text,
  CONSTRAINT professionals_pkey PRIMARY KEY (id),
  CONSTRAINT professionals_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);
CREATE TABLE public.services (
  id integer NOT NULL DEFAULT nextval('services_id_seq'::regclass),
  company_id integer NOT NULL,
  name character varying NOT NULL,
  description text,
  type character varying NOT NULL CHECK (type::text = ANY (ARRAY['corte'::character varying, 'barba'::character varying, 'combo'::character varying, 'sobrancelha'::character varying]::text[])),
  CONSTRAINT services_pkey PRIMARY KEY (id),
  CONSTRAINT services_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);
CREATE TABLE public.users (
  id integer NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  company_id integer NOT NULL,
  name character varying NOT NULL,
  email character varying NOT NULL UNIQUE,
  role character varying NOT NULL CHECK (role::text = ANY (ARRAY['admin'::character varying, 'employee'::character varying]::text[])),
  phone character varying,
  avatar text,
  created_at timestamp without time zone DEFAULT now(),
  CONSTRAINT users_pkey PRIMARY KEY (id),
  CONSTRAINT users_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);