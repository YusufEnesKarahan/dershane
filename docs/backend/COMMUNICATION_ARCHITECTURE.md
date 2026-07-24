# Communication Architecture

The communication flow is DTO → Action → Service → Repository. Email uses Laravel Mail; the SMS adapter is a provider-neutral placeholder. Preferences are checked before delivery and every delivery attempt is recorded.
