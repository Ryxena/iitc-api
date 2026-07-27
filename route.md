# Application User Flow & API Routes

This document outlines the API routes sorted by the typical user journey. You can use this flow to structure your Postman collections logically.

---

## 1. Authentication Flow
The first step for any user is to create an account and authenticate to receive an API token.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/register` | Register a new user account | No |
| `POST` | `/api/login` | Login to receive a Sanctum token | No |
| `GET`  | `/api/verify-email/{id}/{hash}` | Verify the user's email address | No |
| `POST` | `/api/forgot-password` | Request a password reset link | No |
| `POST` | `/api/reset-password` | Set a new password | No |
| `POST` | `/api/logout` | Logout and invalidate the current token | Yes |

---

## 2. Public Discovery
Users can browse the available competitions and categories without needing to be logged in.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET`  | `/api/competitions` | Get a list of all active competitions | No |
| `GET`  | `/api/competitions/{slug}` | Get detailed information about a specific competition | No |
| `GET`  | `/api/competitions/categories` | Get a list of competition categories | No |

---

## 3. User Profile & Dashboard
Once logged in, users can manage their profile and see what they have registered for.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET`  | `/api/profile` | Get the logged-in user's profile and participant data | Yes |
| `POST` | `/api/profile` | Update the user's profile and participant data | Yes |
| `GET`  | `/api/competitions/mine` | Dashboard: Get all teams and competitions the user is part of | Yes |

---

## 4. Participating in Competitions (Team Management)
Users can join individual competitions, create new teams, or join existing teams using an invite code.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/individual/{competitionSlug}` | Register for an individual (1-person) competition | Yes |
| `POST` | `/api/teams/{competitionSlug}` | Create a new team for a group competition | Yes |
| `PUT`  | `/api/teams/join` | Join an existing team using a team code | Yes |
| `GET`  | `/api/teams/{teamId}` | View specific team details (must be leader or member) | Yes |
| `POST` | `/api/teams/{teamId}/update` | Update team name, title, or upload submission (Leader only) | Yes |
| `DELETE`| `/api/teams/{teamId}/members/{memberId}` | Kick a member out of the team (Leader only) | Yes |
| `DELETE`| `/api/teams/{teamId}` | Disband/Delete the entire team (Leader only) | Yes |

---

## 5. Seminar Registration (FREE)
Users can register for the IITC seminar for free. A certificate is auto-generated after admin verifies attendance.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| `POST` | `/api/seminar/register` | Register for the seminar (free, no payment needed) | Yes |
| `GET`  | `/api/seminar/{userId}` | Check the user's registration status and certificate URL | Yes |
| `GET`  | `/api/seminar/{userId}/certificate` | Download the user's PDF certificate | Yes |
| `POST` | `/api/seminar/{userId}/verify-attendance` | [ADMIN] Verify attendance and auto-generate certificate | Yes |

---

## 6. Competition Payments
Once registered for a team, the user must upload payment proof.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/payment/{teamId}` | Upload payment proof for a specific team competition | Yes |
