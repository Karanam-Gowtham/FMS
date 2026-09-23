<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> &mdash; FMS</title>
    <link href="<?= BASE_URL ?>/assets/css/layout.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/nba_module.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../../../includes/header.php'; ?>

<div id="toast" class="toast">Data saved successfully!</div>

<div class="container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/public/index.php?route=dashboard">Dashboard</a> &raquo; 
        <a href="<?= BASE_URL ?>/public/index.php?route=nba/dashboard&year=<?= urlencode($year) ?>">NBA Accreditation</a> &raquo; 
        Criterion 4
    </div>

    <div class="header-row">
        <h1>Criterion 4: Students' Performance</h1>
        <p>Academic Year: <strong><?= htmlspecialchars($year) ?></strong></p>
    </div>

    <div style="margin-bottom: 2rem; background: #fff; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6; display: flex; align-items: center; gap: 15px;">
        <label style="font-weight: 600; color: #495057;">Select Department:</label>
        <select id="level-select" style="padding: 8px; border: 1px solid #ced4da; border-radius: 4px; min-width: 250px;" onchange="switchLevel()">
            <?php foreach ($departments as $d): ?>
                <option value="dept_<?= $d['dept_id'] ?>" <?= $d['dept_id'] == $dept_id ? 'selected' : '' ?>>
                    Department: <?= htmlspecialchars($d['dept_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Table 4A -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4A: Admission details for the program excluding those admitted through multiple entry and exit points.</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.9rem;" border="1">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 30%;">Item (Information is to be provided cumulatively for all the shifts with explicit headings, wherever applicable)</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAY</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm3</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm4 (LYG)</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm5 (LYGm1)</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm6 (LYGm2)</th>
                    </tr>
                </thead>
                <tbody id="table4a-body">
                    <?php 
                    $columns = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
                    $rows = [
                        'N' => 'N= Sanctioned intake of the program (as per AICTE /Competent authority)',
                        'N1' => 'N1= Total no. of students admitted in the 1st year minus the no. of students, who migrated to other programs/ institutions plus no. of students, who migrated to this program',
                        'N2' => 'N2= Number of students admitted in 2nd year in the same batch via lateral entry including leftover seats',
                        'N3' => 'N3= Separate division if any',
                        'N4' => 'N4= Total no. of students admitted in the 1st year via all supernumerary quotas'
                    ];
                    foreach($rows as $key => $label): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6;">
                            <input type="number" id="t4a_<?= $key ?>_<?= $col ?>" class="t4a-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f3f5; font-weight: bold;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Total number of students admitted in the program (N1 + N2 + N3 + N4) - excluding those admitted through multiple entry and exit points.</td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;" id="t4a_total_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="font-size: 0.85rem; color: #6c757d; margin-top: 15px;">
            <p style="margin-bottom: 5px;">CAY= Current Academic Year.<br>
            CAYm1= Current Academic Year Minus 1= Current Assessment Year.<br>
            CAYm2= Current Academic Year Minus 2= Current Assessment Year Minus 1.<br>
            LYG= Last Year Graduate.<br>
            LYGm1= Last Year Graduate Minus 1.<br>
            LYGm2= Last Year Graduate Minus 2.</p>
            <p style="margin-bottom: 10px; color: #333;"><strong>Example for Table No.4A:</strong> Admission details for the program excluding those admitted through multiple entry and exit points.</p>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem; background: #fff;" border="1">
                    <thead style="background: #fff3cd; color: #000;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: left; width: 30%;">Item (Information is to be provided cumulatively for all the shifts with explicit headings, wherever applicable)</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAY<br>2023-24</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm1<br>2022-23</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm2<br>2021-22</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm3<br>2020-21</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm4 (LYG)<br>2019-20</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm5 (LYGm1)<br>2018-19</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm6 (LYGm2)<br>2017-18</th>
                        </tr>
                    </thead>
                    <tbody style="color: #000;">
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N= Sanctioned intake of the program (as per AICTE /Competent authority)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N1= Total no. of students admitted in the 1st year minus the no. of students, who migrated to other programs/ institutions plus no. of students, who migrated to this program</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">116</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N2= Number of students admitted in 2nd year in the same batch via lateral entry including leftover seats</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">11</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">09</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">10</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">11</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">10</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">11</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N3= Separate division if any</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N4= Total no. of students admitted in the 1st year via all supernumerary quotas</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">01</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                        </tr>
                        <tr style="background: #f1f3f5; font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #dee2e6;">Total number of students admitted in the program (N1 + N2 + N3 + N4) - excluding those admitted through multiple entry and exit points.</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">132</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">125</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">130</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">131</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">130</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">131</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Table 4B -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4B: Admission details for the program through multiple entry and exit points.</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.9rem;" border="1">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 30%;">Item (No. of students admitted/exited through multiple entry and exit points) in the respective batch</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAY</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm3</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm4 (LYG)</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm5 (LYGm1)</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm6 (LYGm2)</th>
                    </tr>
                </thead>
                <tbody id="table4b-body">
                    <?php 
                    $rows4b_entry = [
                        'N52' => 'N52= No. of students admitted in 2nd year via multiple entry and exit points in same batch',
                        'N53' => 'N53= No. of students admitted in 3rd year via multiple entry and exit points in same batch',
                        'N54' => 'N54= No. of students admitted in 4th year via multiple entry and exit points in same batch'
                    ];
                    $rows4b_exit = [
                        'N61' => 'N61= No. of students exits after 1st year via multiple entry and exit points in same batch',
                        'N62' => 'N62= No. of students exit after 2nd year via multiple entry and exit points',
                        'N63' => 'N63= No. of students exit after 3rd year via multiple entry and exit points in same batch'
                    ];
                    ?>
                    
                    <tr><td colspan="8" style="background: #e9ecef; font-weight: bold; padding: 5px 10px;">N5 (Multiple entry)</td></tr>
                    <?php foreach($rows4b_entry as $key => $label): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6;">
                            <input type="number" id="t4b_<?= $key ?>_<?= $col ?>" class="t4b-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f3f5; font-weight: bold;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">N5=N52+N53+N54</td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;" id="t4b_total_N5_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>

                    <tr><td colspan="8" style="background: #e9ecef; font-weight: bold; padding: 5px 10px;">N6 (Multiple exit)</td></tr>
                    <?php foreach($rows4b_exit as $key => $label): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;"><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6;">
                            <input type="number" id="t4b_<?= $key ?>_<?= $col ?>" class="t4b-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f1f3f5; font-weight: bold;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">N6=N61+N62+N63</td>
                        <?php foreach($columns as $col): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;" id="t4b_total_N6_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="font-size: 0.85rem; color: #6c757d; margin-top: 15px;">
            <p style="margin-bottom: 10px; color: #333;"><strong>Example for Table No.4B:</strong> Admission details for the program through multiple entry and exit points.</p>
            <div style="overflow-x: auto; margin-bottom: 15px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem; background: #fff;" border="1">
                    <thead style="background: #fff3cd; color: #000;">
                        <tr>
                            <th colspan="2" style="padding: 5px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Item (No. of students admitted/exited through multiple entry and exit points) in the respective batch</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAY<br>2023-24</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm1<br>2022-23</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm2<br>2021-22</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm3<br>2020-21</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm4 (LYG)<br>2019-20</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm5 (LYGm1)<br>2018-19</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm6 (LYGm2)<br>2017-18</th>
                        </tr>
                    </thead>
                    <tbody style="color: #000;">
                        <tr>
                            <td rowspan="4" style="padding: 5px; border: 1px solid #dee2e6; vertical-align: top; width: 15%;">N5(Multiple entry)<br><br>N5=N52+N53+N54</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; width: 25%;">N52= No. of students admitted in 2nd year via multiple entry and exit points in same batch</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2 (a)</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N53= No. of students admitted in 3rd year via multiple entry and exit points in same batch</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1 (b)</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N54= No. of students admitted in 4th year via multiple entry and exit points in same batch</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1 (c)</td>
                        </tr>
                        <tr style="background: #f1f3f5; font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: right;">N5=N52+N53+N54</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">4</td>
                        </tr>
                        <tr>
                            <td rowspan="4" style="padding: 5px; border: 1px solid #dee2e6; vertical-align: top;">N6 (Multiple exit)<br><br>N6=N61+N62+N63</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N61= No. of students exits after 1st year via multiple entry and exit points in same batch</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1 (d)</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N62= No. of students exit after 2nd year via multiple entry and exit points</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1(e)</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N63= No. of students exit after 3rd year via multiple entry and exit points in same batch</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0 (f)</td>
                        </tr>
                        <tr style="background: #f1f3f5; font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: right;">N6=N61+N62+N63</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0(NA)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">2</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <p style="margin-bottom: 5px;"><strong>Example 1:</strong> Multiple entry for Batch LYG m2 (2017-18): No. of students admitted through multiple entry: 4. Breakdown: 2(a) + 1(b) + 1(c), where: a = no. of students admitted in 2nd year, b = no. of students admitted in 3rd year, c = no. of students admitted in 4th year. Therefore, for batch LYG m2 (2017-18): 2 students were admitted in the 2nd year. 1 student was admitted each in the 3rd and 4th years.</p>
            <p><strong>Example 2:</strong> Multiple exit for Batch LYG m2 (2017-18): No. of students exiting/dropped through multiple exit: 2. Breakdown: 1(d) + 1(e) + 0(f), where: d = no. of students exiting after 1st year, e = no. of students exiting after 2nd year, f = no. of students exiting after 3rd year. Therefore, for batch LYG m2 (2017-18): 1 student exited after the 1st year. 1 student exited after the 2nd year. No students exited after the 3rd year.</p>
        </div>
    </div>

    <!-- Table 4C -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4C: No. of students graduated within the stipulated period of the program.</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.9rem;" border="1">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th rowspan="2" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Year of entry</th>
                        <th rowspan="2" style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 30%;">Total no. of students (N1 + N2 + N3+ N4+N5-N6 as defined above)</th>
                        <th colspan="4" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Number of students who have successfully graduated in the stipulated period of study [Total of with Backlogs+ without Backlogs]</th>
                    </tr>
                    <tr>
                        <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">I Year</th>
                        <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">II Year</th>
                        <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">III Year</th>
                        <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">IV Year</th>
                    </tr>
                </thead>
                <tbody id="table4c-body">
                    <?php 
                    $rows4c = [
                        'cay' => 'CAY',
                        'caym1' => 'CAYm1',
                        'caym2' => 'CAYm2',
                        'caym3' => 'CAYm3',
                        'caym4' => 'CAYm4 (LYG)',
                        'caym5' => 'CAYm5 (LYGm1)',
                        'caym6' => 'CAYm6 (LYGm2)'
                    ];
                    $greyed = [
                        'cay' => [1,2,3,4],
                        'caym1' => [2,3,4],
                        'caym2' => [3,4],
                        'caym3' => [4],
                        'caym4' => [],
                        'caym5' => [],
                        'caym6' => []
                    ];
                    ?>
                    <?php foreach($rows4c as $rKey => $label): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; text-align: center;"><?= $label ?></td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: bold;" id="t4c_total_<?= $rKey ?>">0</td>
                        <?php for($i=1; $i<=4; $i++): ?>
                        <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center; <?php if(in_array($i, $greyed[$rKey])) echo 'background: #d6d8db;'; ?>">
                            <?php if(!in_array($i, $greyed[$rKey])): ?>
                                <input type="number" id="t4c_<?= $rKey ?>_year<?= $i ?>" class="t4c-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()">
                            <?php endif; ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="font-size: 0.85rem; color: #6c757d; margin-top: 15px;">
            <p><strong>Example for Table No.4C:</strong> No. of students graduated within the stipulated period of the program.</p>
        </div>
    </div>
    <!-- Section 4.1 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.1 Enrolment Ratio (20)</h3>
        <p style="font-weight: bold; margin-bottom: 10px;">ER Points = Average ER*100</p>
        
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No.4.1.1: Student enrolment ratio in the 1st year.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Item (Students enrolled in the First Year on average over 3 academic years (CAY, CAYm1 and CAYm2))</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAY</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">N= Sanctioned intake of the program in the 1st year (as per AICTE/Competent authority)</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N_cay">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N_caym1">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N_caym2">0</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">N1= Total no. of students admitted in the 1st year minus the no. of students, who migrated to other programs/ institutions plus no. of students, who migrated to this program</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N1_cay">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N1_caym1">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N1_caym2">0</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">N4= Total no. of students admitted in the 1st year via all supernumerary quotas</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N4_cay">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N4_caym1">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t411_N4_caym2">0</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Enrolment Ratio (ER)= (N1+N4)/N</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t411_ER_cay">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t411_ER_caym1">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t411_ER_caym2">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average ER= (ER_1+ ER_2+ ER_3)/3</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t411_Avg_ER">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 25px;">
            <p style="margin-bottom: 5px; color: #333;"><strong>Example for Table No.4.1.1:</strong> Student enrolment ratio in the 1st year.</p>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem; background: #fff;" border="1">
                    <thead style="background: #fff3cd; color: #000;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Item (Students enrolled in the First Year on average over 3 academic years (CAY, CAYm1 and CAYm2))</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAY<br>2023-24</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm1<br>2022-23</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">CAYm2<br>2021-22</th>
                        </tr>
                    </thead>
                    <tbody style="color: #000;">
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N= Sanctioned intake of the program in the 1st year (as per AICTE/Competent authority)</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N1= Total no. of students admitted in the 1st year minus the no. of students, who migrated to other programs/ institutions plus no. of students, who migrated to this program</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">120</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">116</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">N4= Total no. of students admitted in the 1st year via all supernumerary quotas</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">01</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">00</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; border: 1px solid #dee2e6;">Enrolment Ratio (ER)= (N1+N4)/N</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1.00</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">1.01</td>
                            <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0.97</td>
                        </tr>
                        <tr style="background: #f1f3f5; font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #dee2e6;">Average ER = (ER_1+ ER_2+ ER_3)/3</td>
                            <td colspan="3" style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">0.99</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <!-- Section 4.2 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.2. Success Rate of the Students in the Stipulated Period of the Program (15)</h3>
        
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="margin-bottom: 8px;"><strong>Success Rate (SR)</strong> = (No. of students who graduated from the program in the stipulated course duration) /(No. of students admitted in the 1st year of that batch and those actually admitted in the 2nd year via lateral entry, plus the number of students admitted through multiple entry (if any) and separate division if applicable, minus the number of students who exited through multiple entry (if any).</p>
            <p style="margin-bottom: 8px;">Average SR = Mean of SR for the past three batches.</p>
            <p style="font-weight: bold; color: #000;">SR Points = 1.5 * Average SR/10.</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No.4.2.1: The success rate in the stipulated period of a program.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 40%;">Item</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYG</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYGm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYGm2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">A*= (No. of students admitted in the 1st year of that batch and those actually admitted in the 2nd year via lateral entry, plus the number of students admitted through multiple entry (if any) and separate division if applicable, minus the number of students who exited through multiple entry (if any).</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_A_caym4">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_A_caym5">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_A_caym6">0</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">B=No. of students who graduated from the program in the stipulated course duration</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_B_caym4">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_B_caym5">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; background: #e9ecef;" id="t421_B_caym6">0</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Success Rate (SR)= (B/A) * 100</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t421_SR_caym4">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t421_SR_caym5">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t421_SR_caym6">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average SR of three batches ((SR_1+ SR_2+ SR_3)/3)</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t421_Avg_SR">0.00</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size: 0.85rem; color: #6c757d; margin-top: 10px;">
                <p><strong>Note *:</strong> If the value of A in Table No. 4.2.1 is less than the sum of the sanctioned intake (N) and the lateral entry including leftover seats (N2), then the value of A in Table No. 4.2.1 should be the sum of the sanctioned intake (N) and the lateral entry including leftover seats (N2(limited to 10 % of N)).</p>
            </div>
        </div>


    </div>

    <!-- Section 4.3 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.3. Academic Performance of the First-Year Students of the Program (10)</h3>
        
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="margin-bottom: 8px;"><strong>Academic Performance</strong> = Average Academic Performance Index (API), where</p>
            <p style="margin-bottom: 8px;">API = ((Mean of 1st Year Grade Point Average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 1st year/10)) * (Number of successful students/number of students appeared in the examination)</p>
            <p style="margin-bottom: 8px;">Successful students are those who have proceeded to the 2nd year.</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No.4.3.1: Academic Performance of the First-Year Students of the Program.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Academic Performance</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">X= (Mean of 1st year grade point average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 1st year/10)</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_X_caym1" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_X_caym2" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_X_caym3" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Y= Total no. of successful students</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Y_caym1" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Y_caym2" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Y_caym3" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Z = Total no. of students appeared in the examination</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Z_caym1" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Z_caym2" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t431_Z_caym3" class="t431-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">API = X* (Y/Z)</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t431_API_caym1">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t431_API_caym2">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t431_API_caym3">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average API = (AP1 + AP2 + AP3)/3</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t431_Avg_API">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Section 4.4 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.4. Academic Performance of the Second Year Students of the Program (10)</h3>
        
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="margin-bottom: 8px;"><strong>Academic Performance</strong> = Average Academic Performance Index (API), where</p>
            <p style="margin-bottom: 8px;">API = ((Mean of 2nd Year Grade Point Average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 2nd Year/10)) * (Number of successful students/number of students appeared in the examination).</p>
            <p style="margin-bottom: 8px;">Successful students are those who have proceeded to the 3rd year.</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No.4.4.1: Academic Performance of the Second Year Students of the Program.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Academic Performance</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">X= (Mean of 2nd year grade point average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 2nd year/10)</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_X_caym1" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_X_caym2" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_X_caym3" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Y= Total no. of successful students</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Y_caym1" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Y_caym2" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Y_caym3" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Z = Total no. of students appeared in the examination</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Z_caym1" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Z_caym2" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t441_Z_caym3" class="t441-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">API = X* (Y/Z)</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t441_API_caym1">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t441_API_caym2">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t441_API_caym3">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average API = (AP1 + AP2 + AP3)/3</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t441_Avg_API">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4.5 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.5. Academic Performance of the Third Year Students of the Program (10)</h3>
        
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="margin-bottom: 8px;"><strong>Academic Performance</strong> = Average Academic Performance Index (API), where</p>
            <p style="margin-bottom: 8px;">API = ((Mean of 3rd Year Grade Point Average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 3rd Year/10)) * (Number of successful students/number of students appeared in the examination).</p>
            <p style="margin-bottom: 8px;">Successful students are those who have proceeded to the 4th year.</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No.4.5.1: Academic Performance of the Third Year Students of the Program.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Academic Performance</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm2</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">CAYm3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">X= (Mean of 3rd year grade point average of all successful students on a 10-point scale) or (Mean of the percentage of marks of all successful students in 3rd year/10)</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_X_caym1" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_X_caym2" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_X_caym3" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Y= Total no. of successful students</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Y_caym1" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Y_caym2" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Y_caym3" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Z = Total no. of students appeared in the examination</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Z_caym1" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Z_caym2" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t451_Z_caym3" class="t451-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">API = X* (Y/Z)</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t451_API_caym1">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t451_API_caym2">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t451_API_caym3">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average API = (AP1 + AP2 + AP3)/3</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t451_Avg_API">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4.6 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.6. Placement, Higher Studies and Entrepreneurship (30)</h3>
        
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="margin-bottom: 8px;">Placement index points= 0.3 * Average placement index (P).</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.6.1: Placement, higher studies, and entrepreneurship details.</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left; width: 40%;">Item</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYG</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYGm1</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">LYGm2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">FS*=Total no. of final year students</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_FS_caym4" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_FS_caym5" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_FS_caym6" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">X= No. of students placed</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_X_caym4" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_X_caym5" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_X_caym6" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Y= No. of students admitted to higher studies</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Y_caym4" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Y_caym5" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Y_caym6" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Z= No. of students taking up entrepreneurship</td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Z_caym4" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Z_caym5" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <td style="padding: 5px; border: 1px solid #dee2e6;"><input type="number" id="t461_Z_caym6" class="t461-input" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box; text-align: center;" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">X + Y + Z = </td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_Sum_caym4">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_Sum_caym5">0</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_Sum_caym6">0</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Placement Index (P) = (((X + Y + Z)/FS) * 100)</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_P_caym4">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_P_caym5">0.00</td>
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_P_caym6">0.00</td>
                    </tr>
                    <tr style="font-weight: bold; background: #f1f3f5;">
                        <td style="padding: 10px; border: 1px solid #dee2e6;">Average placement index = (P_1 + P_2 + P_3)/3</td>
                        <td colspan="3" style="padding: 10px; border: 1px solid #dee2e6; text-align: center;" id="t461_Avg_P">0.00</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size: 0.85rem; color: #6c757d; margin-top: 10px;">
                <p><strong>Note *:</strong> If the value of FS in Table No. 4.6.1 is less than the sum of the sanctioned intake (N) and the lateral entry including leftover seats (N2), then the value of FS in Table No. 4.6.1 should be the sum of the sanctioned intake (N) and the lateral entry including leftover seats (N2(limited to 10 % of N)).</p>
            </div>
        </div>
    </div>

    <!-- Section 4.7 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.7. Professional Activities (25)</h3>
        
        <h4 style="margin-top: 0;">4.7.1. Professional Societies/ Bodies, Chapters, Clubs, and Professional Engineering Events Organized (05)</h4>
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="color: #0d6efd;">(Provide a list of active professional societies/bodies, chapters, and clubs that exist at the departmental/cluster level in the past 3 years, and also provide a list of events organized by the professional societies, chapters, and clubs over the past 3 years.)</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <p style="font-weight: bold; margin: 0; text-align: center;">Table No. 4.7.1.1: List of active professional societies/bodies/chapters/clubs.</p>
                <button type="button" onclick="addT4711Row()" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                <thead style="background: #fff3cd;">
                    <tr>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 10%;">S.N.</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Name of the Professional Societies/Bodies, Chapters, Clubs</th>
                        <th style="padding: 10px; border: 1px solid #dee2e6; text-align: center; width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody id="t4711-tbody">
                </tbody>
            </table>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.7.1.2: List of events/programs organized.</p>
            
            <!-- CAYm1 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm1</span>
                    <button type="button" onclick="addT4712Row('caym1')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Professional Societies/Bodies/Chapters/Clubs</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">National/International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4712-caym1-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm2 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm2</span>
                    <button type="button" onclick="addT4712Row('caym2')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Professional Societies/Bodies/Chapters/Clubs</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">National/International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4712-caym2-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm3 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm3</span>
                    <button type="button" onclick="addT4712Row('caym3')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Professional Societies/Bodies/Chapters/Clubs</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">National/International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4712-caym3-tbody"></tbody>
                </table>
            </div>
        </div>

        <h4 style="margin-top: 2rem;">4.7.2. Student's Participations in Professional Events (10)</h4>
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="color: #0d6efd;">(Provide details of students, who have participated at other institutes in various professional events, such as hackathons, codeathons, ideathons, etc., over the past 3 years.)</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.7.2.1: List of students participated in professional events.</p>
            
            <!-- CAYm1 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm1</span>
                    <button type="button" onclick="addT4721Row('caym1')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">State /National/ International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4721-caym1-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm2 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm2</span>
                    <button type="button" onclick="addT4721Row('caym2')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">State /National/ International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4721-caym2-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm3 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm3</span>
                    <button type="button" onclick="addT4721Row('caym3')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">State /National/ International level</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Date of Event</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4721-caym3-tbody"></tbody>
                </table>
            </div>
        </div>

        <h4 style="margin-top: 2rem;">4.7.3. Publication of Journals, Magazines, Newsletters, etc. in the Department (05)</h4>
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="color: #0d6efd;">(Provide details of journals, magazines, newsletters, etc., published by the department, along with the names of the editors, issue numbers, volume numbers, and a list of students involved for the past 3 years.)</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.7.3.1: List of students involved in publication of journals, magazines, and newsletters, etc. in the Department.</p>
            
            <!-- CAYm1 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm1</span>
                    <button type="button" onclick="addT4731Row('caym1')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal, Magazine, Newsletter</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Editor</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">No. of Issues</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Hard copy/ Soft copy</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4731-caym1-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm2 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm2</span>
                    <button type="button" onclick="addT4731Row('caym2')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal, Magazine, Newsletter</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Editor</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">No. of Issues</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Hard copy/ Soft copy</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4731-caym2-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm3 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm3</span>
                    <button type="button" onclick="addT4731Row('caym3')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal, Magazine, Newsletter</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Editor</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">No. of Issues</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Hard copy/ Soft copy</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4731-caym3-tbody"></tbody>
                </table>
            </div>
        </div>

        <h4 style="margin-top: 2rem;">4.7.4. Student Publications (05)</h4>
        <div style="font-size: 0.9rem; margin-bottom: 15px; color: #495057;">
            <p style="color: #0d6efd;">(Provide details of student publications in journals, conferences, etc., for the past 3 years.)</p>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.7.4.1: List of student publications.</p>
            
            <!-- CAYm1 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm1</span>
                    <button type="button" onclick="addT4741Row('caym1')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Publisher</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal/ Conference, etc.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Volume No. & Issue No.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4741-caym1-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm2 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm2</span>
                    <button type="button" onclick="addT4741Row('caym2')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Publisher</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal/ Conference, etc.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Volume No. & Issue No.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4741-caym2-tbody"></tbody>
                </table>
            </div>

            <!-- CAYm3 Table -->
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                    <span>CAYm3</span>
                    <button type="button" onclick="addT4741Row('caym3')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 5%;">S.N.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Student & Semester</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Publisher</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Journal/ Conference, etc.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Volume No. & Issue No.</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6;">Name of the Award if any</th>
                            <th style="padding: 5px; border: 1px solid #dee2e6; width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4741-caym3-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 4rem;">
        <button type="button" class="btn-save" onclick="saveData()">Save Criterion 4</button>
    </div>

</div>

<script>
    const year = <?= json_encode($year) ?>;
    const initialData = <?= json_encode($criteria_data) ?>;
    let levelData = {};
    let currentLevel = '';

    function initData() {
        levelData = initialData.levelData || {};
        
        const defaultDept = 'dept_<?= $dept_id ?>';
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { pdf_4_1: '', pdf_4_2: '', pdf_4_3: '', pdf_4_4: '', pdf_4_5: '', pdf_4_6: '', table4a: {}, t4711: [], t4712: {caym1: [], caym2: [], caym3: []}, t4721: {caym1: [], caym2: [], caym3: []}, t4731: {caym1: [], caym2: [], caym3: []}, t4741: {caym1: [], caym2: [], caym3: []} };
        }

        switchLevel();
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        currentLevel = select.value;
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { pdf_4_1: '', pdf_4_2: '', pdf_4_3: '', pdf_4_4: '', pdf_4_5: '', pdf_4_6: '', table4a: {}, t4711: [], t4712: {caym1: [], caym2: [], caym3: []}, t4721: {caym1: [], caym2: [], caym3: []}, t4731: {caym1: [], caym2: [], caym3: []}, t4741: {caym1: [], caym2: [], caym3: []} };
        }
        if (!levelData[currentLevel].table4a) {
            levelData[currentLevel].table4a = {};
        }
        if (!levelData[currentLevel].t4711) levelData[currentLevel].t4711 = [];
        if (!levelData[currentLevel].t4712) levelData[currentLevel].t4712 = {caym1: [], caym2: [], caym3: []};
        if (!levelData[currentLevel].t4721) levelData[currentLevel].t4721 = {caym1: [], caym2: [], caym3: []};
        if (!levelData[currentLevel].t4731) levelData[currentLevel].t4731 = {caym1: [], caym2: [], caym3: []};
        if (!levelData[currentLevel].t4741) levelData[currentLevel].t4741 = {caym1: [], caym2: [], caym3: []};

        // Populate Table 4A, 4B, and 4C
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        const keys4a = ['N', 'N1', 'N2', 'N3', 'N4'];
        const keys4b = ['N52', 'N53', 'N54', 'N61', 'N62', 'N63'];
        
        keys4a.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4a_${k}_${c}`);
                if (el) {
                    el.value = levelData[currentLevel].table4a[`${k}_${c}`] || '';
                }
            });
        });
        keys4b.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4b_${k}_${c}`);
                if (el) {
                    el.value = levelData[currentLevel].table4a[`${k}_${c}`] || '';
                }
            });
        });
        cols.forEach(r => {
            for(let i=1; i<=4; i++) {
                const el = document.getElementById(`t4c_${r}_year${i}`);
                if (el) {
                    el.value = levelData[currentLevel].table4a[`t4c_${r}_year${i}`] || '';
                }
            }
        });

        // Load 4.3.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t431_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`t431_${k}_${c}`] || '';
            });
        });

        // Load 4.4.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t441_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`t441_${k}_${c}`] || '';
            });
        });

        // Load 4.5.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t451_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`t451_${k}_${c}`] || '';
            });
        });

        // Load 4.6.1
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            ['FS', 'X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t461_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`t461_${k}_${c}`] || '';
            });
        });

        calculateAllTotals();
        renderT4711();
        renderT4712();
        renderT4721();
        renderT4731();
        renderT4741();
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) return;
        if (!levelData[currentLevel].table4a) levelData[currentLevel].table4a = {};
        
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        const keys4a = ['N', 'N1', 'N2', 'N3', 'N4'];
        const keys4b = ['N52', 'N53', 'N54', 'N61', 'N62', 'N63'];
        
        keys4a.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4a_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`${k}_${c}`] = el.value;
                }
            });
        });
        keys4b.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4b_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`${k}_${c}`] = el.value;
                }
            });
        });
        cols.forEach(r => {
            for(let i=1; i<=4; i++) {
                const el = document.getElementById(`t4c_${r}_year${i}`);
                if (el) {
                    levelData[currentLevel].table4a[`t4c_${r}_year${i}`] = el.value;
                }
            }
        });

        // Save 4.3.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t431_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`t431_${k}_${c}`] = el.value;
                }
            });
        });

        // Save 4.4.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t441_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`t441_${k}_${c}`] = el.value;
                }
            });
        });

        // Save 4.5.1
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            ['X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t451_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`t451_${k}_${c}`] = el.value;
                }
            });
        });

        // Save 4.6.1
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            ['FS', 'X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t461_${k}_${c}`);
                if (el) {
                    levelData[currentLevel].table4a[`t461_${k}_${c}`] = el.value;
                }
            });
        });
    }

    function calculateAllTotals() {
        calculateT4aTotals();
        calculateT4bTotals();
        calculateT4cTotals();
        calculateT411();
        calculateT421();
        calculateT431();
        calculateT441();
        calculateT451();
        calculateT461();
    }

    function calculateT4aTotals() {
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        cols.forEach(c => {
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`).value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`).value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`).value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`).value) || 0;
            const totalEl = document.getElementById(`t4a_total_${c}`);
            if (totalEl) totalEl.innerText = (n1 + n2 + n3 + n4);
        });
    }

    function calculateT4bTotals() {
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        cols.forEach(c => {
            const n52 = parseInt(document.getElementById(`t4b_N52_${c}`).value) || 0;
            const n53 = parseInt(document.getElementById(`t4b_N53_${c}`).value) || 0;
            const n54 = parseInt(document.getElementById(`t4b_N54_${c}`).value) || 0;
            const totalN5 = document.getElementById(`t4b_total_N5_${c}`);
            if (totalN5) totalN5.innerText = (n52 + n53 + n54);
            
            const n61 = parseInt(document.getElementById(`t4b_N61_${c}`).value) || 0;
            const n62 = parseInt(document.getElementById(`t4b_N62_${c}`).value) || 0;
            const n63 = parseInt(document.getElementById(`t4b_N63_${c}`).value) || 0;
            const totalN6 = document.getElementById(`t4b_total_N6_${c}`);
            if (totalN6) totalN6.innerText = (n61 + n62 + n63);
        });
    }

    function calculateT4cTotals() {
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        cols.forEach(c => {
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`).value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`).value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`).value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`).value) || 0;
            
            const n52 = parseInt(document.getElementById(`t4b_N52_${c}`).value) || 0;
            const n53 = parseInt(document.getElementById(`t4b_N53_${c}`).value) || 0;
            const n54 = parseInt(document.getElementById(`t4b_N54_${c}`).value) || 0;
            const n5 = n52 + n53 + n54;
            
            const n61 = parseInt(document.getElementById(`t4b_N61_${c}`).value) || 0;
            const n62 = parseInt(document.getElementById(`t4b_N62_${c}`).value) || 0;
            const n63 = parseInt(document.getElementById(`t4b_N63_${c}`).value) || 0;
            const n6 = n61 + n62 + n63;
            
            const total = n1 + n2 + n3 + n4 + n5 - n6;
            
            const totalEl = document.getElementById(`t4c_total_${c}`);
            if (totalEl) totalEl.innerText = total;
        });
    }

    function calculateT411() {
        let totalER = 0;
        let validERs = 0;
        
        ['cay', 'caym1', 'caym2'].forEach(c => {
            const n = parseInt(document.getElementById(`t4a_N_${c}`).value) || 0;
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`).value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`).value) || 0;
            
            const cellN = document.getElementById(`t411_N_${c}`);
            const cellN1 = document.getElementById(`t411_N1_${c}`);
            const cellN4 = document.getElementById(`t411_N4_${c}`);
            const cellER = document.getElementById(`t411_ER_${c}`);
            
            if (cellN) cellN.innerText = n;
            if (cellN1) cellN1.innerText = n1;
            if (cellN4) cellN4.innerText = n4;
            
            let er = 0;
            if (n > 0) {
                er = (n1 + n4) / n;
                if (cellER) cellER.innerText = er.toFixed(2);
                totalER += er;
                validERs++;
            } else {
                if (cellER) cellER.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t411_Avg_ER`);
        if (cellAvg) {
            cellAvg.innerText = validERs > 0 ? (totalER / 3).toFixed(2) : '0.00';
        }
    }

    function calculateT421() {
        let totalSR = 0;
        let validSRs = 0;
        
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            const n = parseInt(document.getElementById(`t4a_N_${c}`).value) || 0;
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`).value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`).value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`).value) || 0;
            
            const n52 = parseInt(document.getElementById(`t4b_N52_${c}`).value) || 0;
            const n53 = parseInt(document.getElementById(`t4b_N53_${c}`).value) || 0;
            const n54 = parseInt(document.getElementById(`t4b_N54_${c}`).value) || 0;
            const n5 = n52 + n53 + n54;
            
            const n61 = parseInt(document.getElementById(`t4b_N61_${c}`).value) || 0;
            const n62 = parseInt(document.getElementById(`t4b_N62_${c}`).value) || 0;
            const n63 = parseInt(document.getElementById(`t4b_N63_${c}`).value) || 0;
            const n6 = n61 + n62 + n63;
            
            let a = n1 + n2 + n3 + n5 - n6;
            
            const threshold = n + n2;
            if (a < threshold) {
                const limitedN2 = Math.min(n2, n * 0.1);
                a = n + limitedN2;
            }
            
            let b = 0;
            for (let i = 1; i <= 4; i++) {
                const val = parseInt(document.getElementById(`t4c_${c}_year${i}`).value) || 0;
                b += val;
            }
            
            const cellA = document.getElementById(`t421_A_${c}`);
            const cellB = document.getElementById(`t421_B_${c}`);
            const cellSR = document.getElementById(`t421_SR_${c}`);
            
            if (cellA) cellA.innerText = Math.round(a);
            if (cellB) cellB.innerText = b;
            
            let sr = 0;
            if (a > 0) {
                sr = (b / a) * 100;
                if (cellSR) cellSR.innerText = sr.toFixed(2);
                totalSR += sr;
                validSRs++;
            } else {
                if (cellSR) cellSR.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t421_Avg_SR`);
        if (cellAvg) {
            cellAvg.innerText = validSRs > 0 ? (totalSR / 3).toFixed(2) : '0.00';
        }
    }

    function calculateT431() {
        let totalAPI = 0;
        let validAPIs = 0;
        
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            const x = parseFloat(document.getElementById(`t431_X_${c}`).value) || 0;
            const y = parseInt(document.getElementById(`t431_Y_${c}`).value) || 0;
            const z = parseInt(document.getElementById(`t431_Z_${c}`).value) || 0;
            
            const cellAPI = document.getElementById(`t431_API_${c}`);
            
            let api = 0;
            if (z > 0) {
                api = x * (y / z);
                if (cellAPI) cellAPI.innerText = api.toFixed(2);
                totalAPI += api;
                validAPIs++;
            } else {
                if (cellAPI) cellAPI.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t431_Avg_API`);
        if (cellAvg) {
            cellAvg.innerText = validAPIs > 0 ? (totalAPI / 3).toFixed(2) : '0.00';
        }
    }

    function calculateT441() {
        let totalAPI = 0;
        let validAPIs = 0;
        
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            const x = parseFloat(document.getElementById(`t441_X_${c}`).value) || 0;
            const y = parseInt(document.getElementById(`t441_Y_${c}`).value) || 0;
            const z = parseInt(document.getElementById(`t441_Z_${c}`).value) || 0;
            
            const cellAPI = document.getElementById(`t441_API_${c}`);
            
            let api = 0;
            if (z > 0) {
                api = x * (y / z);
                if (cellAPI) cellAPI.innerText = api.toFixed(2);
                totalAPI += api;
                validAPIs++;
            } else {
                if (cellAPI) cellAPI.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t441_Avg_API`);
        if (cellAvg) {
            cellAvg.innerText = validAPIs > 0 ? (totalAPI / 3).toFixed(2) : '0.00';
        }
    }

    function calculateT451() {
        let totalAPI = 0;
        let validAPIs = 0;
        
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            const x = parseFloat(document.getElementById(`t451_X_${c}`).value) || 0;
            const y = parseInt(document.getElementById(`t451_Y_${c}`).value) || 0;
            const z = parseInt(document.getElementById(`t451_Z_${c}`).value) || 0;
            
            const cellAPI = document.getElementById(`t451_API_${c}`);
            
            let api = 0;
            if (z > 0) {
                api = x * (y / z);
                if (cellAPI) cellAPI.innerText = api.toFixed(2);
                totalAPI += api;
                validAPIs++;
            } else {
                if (cellAPI) cellAPI.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t451_Avg_API`);
        if (cellAvg) {
            cellAvg.innerText = validAPIs > 0 ? (totalAPI / 3).toFixed(2) : '0.00';
        }
    }

    function calculateT461() {
        let totalP = 0;
        let validPs = 0;
        
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            const n = parseInt(document.getElementById(`t4a_N_${c}`).value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`).value) || 0;

            let fs = parseInt(document.getElementById(`t461_FS_${c}`).value) || 0;
            const x = parseInt(document.getElementById(`t461_X_${c}`).value) || 0;
            const y = parseInt(document.getElementById(`t461_Y_${c}`).value) || 0;
            const z = parseInt(document.getElementById(`t461_Z_${c}`).value) || 0;

            const sum = x + y + z;
            const cellSum = document.getElementById(`t461_Sum_${c}`);
            if (cellSum) cellSum.innerText = sum;

            const threshold = n + n2;
            if (fs < threshold) {
                const limitedN2 = Math.min(n2, n * 0.1);
                fs = n + limitedN2;
            }

            const cellP = document.getElementById(`t461_P_${c}`);
            let p = 0;
            if (fs > 0) {
                p = (sum / fs) * 100;
                if (cellP) cellP.innerText = p.toFixed(2);
                totalP += p;
                validPs++;
            } else {
                if (cellP) cellP.innerText = '0.00';
            }
        });
        
        const cellAvg = document.getElementById(`t461_Avg_P`);
        if (cellAvg) {
            cellAvg.innerText = validPs > 0 ? (totalP / 3).toFixed(2) : '0.00';
        }
    }

    function updatePDFUI(section) {
        const path = levelData[currentLevel]['pdf_' + section];
        const preview = document.getElementById('preview_' + section);
        const link = document.getElementById('link_' + section);
        const dropzone = document.getElementById('pdfInput_' + section).parentElement;

        if (path) {
            link.href = '<?= BASE_URL ?>/' + path;
            preview.style.display = 'flex';
            dropzone.style.display = 'none';
        } else {
            preview.style.display = 'none';
            dropzone.style.display = 'block';
        }
    }

    async function handlePDFUpload(input, section) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed.');
            input.value = '';
            return;
        }

        const overlay = document.getElementById('overlay_' + section);
        overlay.style.display = 'flex';

        const formData = new FormData();
        formData.append('pdf_file', file);
        formData.append('section', section);

        try {
            const response = await fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/upload_pdf&id=4', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                levelData[currentLevel]['pdf_' + section] = result.file_path;
                updatePDFUI(section);
                saveData(true); // Auto-save after successful upload
            } else {
                alert('Upload failed: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred during upload.');
        } finally {
            overlay.style.display = 'none';
            input.value = ''; // Reset input
        }
    }

    function removePDF(section) {
        if(confirm("Are you sure you want to remove this document?")) {
            levelData[currentLevel]['pdf_' + section] = '';
            updatePDFUI(section);
            saveData(true); // Auto-save after removal
        }
    }

    function saveData(silent = false) {
        const formData = new URLSearchParams();
        formData.append('year', year);
        
        const payload = { levelData: levelData };
        formData.append('json_data', JSON.stringify(payload));

        fetch('<?= BASE_URL ?>/public/index.php?route=api/nba/save&id=4', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (!silent) {
                    const toast = document.getElementById('toast');
                    toast.style.display = 'block';
                    setTimeout(() => toast.style.display = 'none', 3000);
                }
            } else {
                alert('Error saving data: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to communicate with server.');
        });
    }

    // Dynamic Tables Logic for 4.7.1
    function renderT4711() {
        const tbody = document.getElementById('t4711-tbody');
        if(!tbody) return;
        tbody.innerHTML = '';
        if (!levelData[currentLevel].t4711) levelData[currentLevel].t4711 = [];
        
        levelData[currentLevel].t4711.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">${index + 1}</td>
                <td style="padding: 5px; border: 1px solid #dee2e6;">
                    <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.name || ''}" onchange="updateT4711(${index}, 'name', this.value)">
                </td>
                <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">
                    <button type="button" onclick="removeT4711Row(${index})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function addT4711Row() {
        if (!levelData[currentLevel].t4711) levelData[currentLevel].t4711 = [];
        levelData[currentLevel].t4711.push({ name: '' });
        renderT4711();
        saveData(true);
    }

    function updateT4711(index, field, value) {
        levelData[currentLevel].t4711[index][field] = value;
        saveData(true);
    }

    function removeT4711Row(index) {
        if(confirm("Remove this row?")) {
            levelData[currentLevel].t4711.splice(index, 1);
            renderT4711();
            saveData(true);
        }
    }

    function renderT4712() {
        if (!levelData[currentLevel].t4712) levelData[currentLevel].t4712 = { caym1: [], caym2: [], caym3: [] };
        
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4712-${year}-tbody`);
            if(!tbody) return;
            tbody.innerHTML = '';
            
            levelData[currentLevel].t4712[year].forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">${index + 1}</td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.society || ''}" onchange="updateT4712('${year}', ${index}, 'society', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.event_name || ''}" onchange="updateT4712('${year}', ${index}, 'event_name', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <select style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" onchange="updateT4712('${year}', ${index}, 'level', this.value)">
                            <option value="">Select Level</option>
                            <option value="National" ${row.level === 'National' ? 'selected' : ''}>National</option>
                            <option value="International" ${row.level === 'International' ? 'selected' : ''}>International</option>
                            <option value="State/Local" ${row.level === 'State/Local' ? 'selected' : ''}>State/Local</option>
                        </select>
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="date" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.date || ''}" onchange="updateT4712('${year}', ${index}, 'date', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">
                        <button type="button" onclick="removeT4712Row('${year}', ${index})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4712Row(year) {
        if (!levelData[currentLevel].t4712) levelData[currentLevel].t4712 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4712[year]) levelData[currentLevel].t4712[year] = [];
        levelData[currentLevel].t4712[year].push({ society: '', event_name: '', level: '', date: '' });
        renderT4712();
        saveData(true);
    }

    function updateT4712(year, index, field, value) {
        levelData[currentLevel].t4712[year][index][field] = value;
        saveData(true);
    }

    function removeT4712Row(year, index) {
        if(confirm("Remove this row?")) {
            levelData[currentLevel].t4712[year].splice(index, 1);
            renderT4712();
            saveData(true);
        }
    }

    function renderT4721() {
        if (!levelData[currentLevel].t4721) levelData[currentLevel].t4721 = { caym1: [], caym2: [], caym3: [] };
        
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4721-${year}-tbody`);
            if(!tbody) return;
            tbody.innerHTML = '';
            
            levelData[currentLevel].t4721[year].forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">${index + 1}</td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.student_name || ''}" onchange="updateT4721('${year}', ${index}, 'student_name', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.event_name || ''}" onchange="updateT4721('${year}', ${index}, 'event_name', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <select style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" onchange="updateT4721('${year}', ${index}, 'level', this.value)">
                            <option value="">Select Level</option>
                            <option value="State" ${row.level === 'State' ? 'selected' : ''}>State</option>
                            <option value="National" ${row.level === 'National' ? 'selected' : ''}>National</option>
                            <option value="International" ${row.level === 'International' ? 'selected' : ''}>International</option>
                        </select>
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="date" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.date || ''}" onchange="updateT4721('${year}', ${index}, 'date', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.award || ''}" onchange="updateT4721('${year}', ${index}, 'award', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">
                        <button type="button" onclick="removeT4721Row('${year}', ${index})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4721Row(year) {
        if (!levelData[currentLevel].t4721) levelData[currentLevel].t4721 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4721[year]) levelData[currentLevel].t4721[year] = [];
        levelData[currentLevel].t4721[year].push({ student_name: '', event_name: '', level: '', date: '', award: '' });
        renderT4721();
        saveData(true);
    }

    function updateT4721(year, index, field, value) {
        levelData[currentLevel].t4721[year][index][field] = value;
        saveData(true);
    }

    function removeT4721Row(year, index) {
        if(confirm("Remove this row?")) {
            levelData[currentLevel].t4721[year].splice(index, 1);
            renderT4721();
            saveData(true);
        }
    }

    function renderT4731() {
        if (!levelData[currentLevel].t4731) levelData[currentLevel].t4731 = { caym1: [], caym2: [], caym3: [] };
        
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4731-${year}-tbody`);
            if(!tbody) return;
            tbody.innerHTML = '';
            
            levelData[currentLevel].t4731[year].forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">${index + 1}</td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.journal_name || ''}" onchange="updateT4731('${year}', ${index}, 'journal_name', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.editor || ''}" onchange="updateT4731('${year}', ${index}, 'editor', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.student_semester || ''}" onchange="updateT4731('${year}', ${index}, 'student_semester', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="number" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.issues || ''}" onchange="updateT4731('${year}', ${index}, 'issues', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <select style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" onchange="updateT4731('${year}', ${index}, 'copy_type', this.value)">
                            <option value="">Select</option>
                            <option value="Hard Copy" ${row.copy_type === 'Hard Copy' ? 'selected' : ''}>Hard Copy</option>
                            <option value="Soft Copy" ${row.copy_type === 'Soft Copy' ? 'selected' : ''}>Soft Copy</option>
                            <option value="Both" ${row.copy_type === 'Both' ? 'selected' : ''}>Both</option>
                        </select>
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">
                        <button type="button" onclick="removeT4731Row('${year}', ${index})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4731Row(year) {
        if (!levelData[currentLevel].t4731) levelData[currentLevel].t4731 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4731[year]) levelData[currentLevel].t4731[year] = [];
        levelData[currentLevel].t4731[year].push({ journal_name: '', editor: '', student_semester: '', issues: '', copy_type: '' });
        renderT4731();
        saveData(true);
    }

    function updateT4731(year, index, field, value) {
        levelData[currentLevel].t4731[year][index][field] = value;
        saveData(true);
    }

    function removeT4731Row(year, index) {
        if(confirm("Remove this row?")) {
            levelData[currentLevel].t4731[year].splice(index, 1);
            renderT4731();
            saveData(true);
        }
    }

    function renderT4741() {
        if (!levelData[currentLevel].t4741) levelData[currentLevel].t4741 = { caym1: [], caym2: [], caym3: [] };
        
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4741-${year}-tbody`);
            if(!tbody) return;
            tbody.innerHTML = '';
            
            levelData[currentLevel].t4741[year].forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">${index + 1}</td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.student_semester || ''}" onchange="updateT4741('${year}', ${index}, 'student_semester', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.publisher || ''}" onchange="updateT4741('${year}', ${index}, 'publisher', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.journal || ''}" onchange="updateT4741('${year}', ${index}, 'journal', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.volume_issue || ''}" onchange="updateT4741('${year}', ${index}, 'volume_issue', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6;">
                        <input type="text" style="width: 100%; border: 1px solid #ced4da; padding: 5px; box-sizing: border-box;" value="${row.award || ''}" onchange="updateT4741('${year}', ${index}, 'award', this.value)">
                    </td>
                    <td style="padding: 5px; border: 1px solid #dee2e6; text-align: center;">
                        <button type="button" onclick="removeT4741Row('${year}', ${index})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4741Row(year) {
        if (!levelData[currentLevel].t4741) levelData[currentLevel].t4741 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4741[year]) levelData[currentLevel].t4741[year] = [];
        levelData[currentLevel].t4741[year].push({ student_semester: '', publisher: '', journal: '', volume_issue: '', award: '' });
        renderT4741();
        saveData(true);
    }

    function updateT4741(year, index, field, value) {
        levelData[currentLevel].t4741[year][index][field] = value;
        saveData(true);
    }

    function removeT4741Row(year, index) {
        if(confirm("Remove this row?")) {
            levelData[currentLevel].t4741[year].splice(index, 1);
            renderT4741();
            saveData(true);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', initData);

</script>
</body>
</html>
