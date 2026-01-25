<?php
    include 'header_hod.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCorp - Contact Us</title>
    <link rel="stylesheet" href="../css/acd_year1.css">
</head>
<body>
    <main class="hero">
        <div class="container">
            <div class="contact-wrapper">
                    
                    <div class="contact-form">
                            <h1>AQARs Supporting Documents</h1>
                            <form action="files_fac.php" method="POST">
                            <div class="form-group">
                                <label for="academic-year">Select Academic Year:</label>
                                <select name="year" id="academic-year" required>
                                    <option value="" disabled selected>Select an academic year</option>
                                    <option value="2022-23">2022-23</option>
                                    <option value="2021-22">2021-22</option>
                                    <option value="2020-21">2020-21</option>
                                </select>
                                </div>
                                <div class="form-group">
                                <label for="criteria">Select Criteria:</label>
                                <select name="criteria" id="criteria" required>
                                    <option value="" disabled selected>Select a criteria</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                </select>
                                </div>
                                <button type="submit" class="button1">Enter</button>
                            </form>
                    
                </div>

                <div class="contact-form">
                <div class="content">
                        <h1>Achievements</h1>
                        
                        <div class="buttons">
                            <a href="fdps_down.php" class="btn fdps">FDPS Attended</a><br><br><br>
                            <a href="fdps_org_down.php" class="btn fdps_org">FDPS Organised</a><br><br><br>
                            <a href="published_down.php" class="btn papers">Papers Published</a><br><br><br>
                            <a href="conference_down.php" class="btn papers">Conferences Published</a><br><br><br>
                            <a href="patents_down.php" class="btn patents">Patents</a>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    
                    <h1>Department Files</h1>
                    <div class="buttons1">
                        <a href="down_dept_files.php?event=admin" class="btn admin-files">Admin Files</a><br><br><br>
                        <a href="down_dept_files.php?event=faculty" class="btn faculty-files">Faculty Files</a><br><br><br>
                        <a href="down_dept_files.php?event=student" class="btn student-files">Student Files</a><br><br><br>
                        <a href="down_dept_files.php?event=exam" class="btn exam-files">Exam Section Files</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

