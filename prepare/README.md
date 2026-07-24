# FMS — Project Preparation Guide (End-to-End Explanation)

> **⚠️ This folder is NOT connected to the FMS project code. It is standalone documentation for project presentation/viva preparation. Remove or cut this folder before final submission.**

---

## 📋 Contents

| # | File | Topic | Description |
|:---:|:---|:---|:---|
| 1 | [01_Problem_Statement.md](./01_Problem_Statement.md) | **Problem Statement** | Background, the 4 core problems, objectives, scope, target users, expected outcomes |
| 2 | [02_Architecture_and_Design.md](./02_Architecture_and_Design.md) | **Architecture & Design** | 3-tier architecture, module layout, DB design (30+ tables), access control layers, access matrix, design patterns |
| 3 | [03_Workflow.md](./03_Workflow.md) | **Workflow** | Authentication flows, document lifecycle (Pending→Approved/Rejected), faculty upload flow, dashboard workflow, notification flow, bulk operations, central coordinator flow |
| 4 | [04_Technology_Stack.md](./04_Technology_Stack.md) | **Technology Stack** | PHP, MySQL, HTML/CSS/JS, Bootstrap, pdf-lib, PHPMailer, development & deployment environments, security technologies |
| 5 | [05_Individual_Contribution.md](./05_Individual_Contribution.md) | **Individual Contribution** | Specific modules authored, ~1,875 lines of code, key technical decisions (customize team member names!) |
| 6 | [06_Challenges_Faced.md](./06_Challenges_Faced.md) | **Challenges Faced** | 9 real challenges with problem → solution → lesson learned format |
| 7 | [07_Testing_and_Outcomes.md](./07_Testing_and_Outcomes.md) | **Testing & Outcomes** | 40+ test cases across 7 categories, browser compatibility, performance metrics, quantitative results, known limitations, future enhancements |

---

## 🎤 Quick Viva Answers

### "What is your project?"
> FMS is a Role-Based Document Management System for GMRIT that digitizes the entire lifecycle of academic proofs — from faculty upload to HOD/Department Coordinator approval — mapped to NAAC/NBA accreditation criteria.

### "Why did you build this?"
> GMRIT's accreditation process relied on manual paper-based document collection across departments. Faculty would email files, coordinators would organize them in folders, and there was no approval tracking. FMS eliminates this chaos with a structured digital workflow.

### "What technology did you use?"
> PHP 8.x (vanilla, no framework) for the backend, MySQL/MariaDB for the database, HTML5/CSS3/JavaScript for the frontend with Bootstrap for responsive design, and pdf-lib for client-side PDF merging.

### "What was the hardest part?"
> Building the unified dashboard that queries 16+ different database tables (each with different column names) in a single UNION ALL query, while enforcing department-level access control so HODs only see their department's files.

### "How does the approval workflow work?"
> Faculty uploads a document → status is set to "Pending HOD" → HOD can approve (→ "Pending Dept Coordinator") or reject (with reason) → Dept Coordinator can approve (→ "Accepted") or reject → Faculty can re-upload rejected files, which resets the cycle.

### "What security measures did you implement?"
> CSRF protection on all POST forms, prepared statements (mysqli) to prevent SQL injection, session-based role detection, department-level data isolation via dept_scope.php, and file path verification against database records.

---

## ✏️ Customization Notes

1. **Team Member Names**: Edit `05_Individual_Contribution.md` and replace `[Your Name]`, `Team Member 2`, `Team Member 3` with actual names.
2. **Contribution Split**: Adjust which modules you claim based on your actual work.
3. **Institution Name**: The docs reference GMRIT — update if your institution is different.
4. **Performance Numbers**: The metrics in `07_Testing_and_Outcomes.md` are based on local XAMPP testing — adjust if you have production benchmarks.
