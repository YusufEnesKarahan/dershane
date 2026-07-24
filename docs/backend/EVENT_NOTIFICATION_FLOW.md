# Event Notification Flow

`StudentRegistered`, `PaymentOverdue`, `ExamResultPublished`, `HomeworkAssigned`, and `CrmFollowupDue` originate in model observers. `CreateDomainNotification` translates them into typed panel notifications for administrator recipients. This keeps education, finance, and CRM independent from delivery channels.
