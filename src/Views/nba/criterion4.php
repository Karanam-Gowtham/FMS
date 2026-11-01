<?php
$criterion_num = 4;
$criterion_title = "Students' Performance";
include __DIR__ . '/../partials/nba_header.php';

$columns = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
$years3  = ['caym1' => 'CAYm1', 'caym2' => 'CAYm2', 'caym3' => 'CAYm3'];
?>

    <!-- Table 4A -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4A: Admission details for the program excluding those admitted through multiple entry and exit points.</h3>
        <div style="overflow-x: auto;">
            <table class="nba-table">
                <thead>
                    <tr>
                        <th scope="col" class="cell-left" style="width: 30%;">Item (Information is to be provided cumulatively for all the shifts with explicit headings, wherever applicable)</th>
                        <th scope="col" class="cell-center">CAY</th>
                        <th scope="col" class="cell-center">CAYm1</th>
                        <th scope="col" class="cell-center">CAYm2</th>
                        <th scope="col" class="cell-center">CAYm3</th>
                        <th scope="col" class="cell-center">CAYm4 (LYG)</th>
                        <th scope="col" class="cell-center">CAYm5 (LYGm1)</th>
                        <th scope="col" class="cell-center">CAYm6 (LYGm2)</th>
                    </tr>
                </thead>
                <tbody id="table4a-body">
                    <?php 
                    $rows4a = [
                        'N'  => 'N= Sanctioned intake of the program (as per AICTE /Competent authority)',
                        'N1' => 'N1= Total no. of students admitted in the 1st year minus the no. of students who migrated to other programs/ institutions plus no. of students who migrated to this program',
                        'N2' => 'N2= Number of students admitted in 2nd year in the same batch via lateral entry including leftover seats',
                        'N3' => 'N3= Separate division if any',
                        'N4' => 'N4= Total no. of students admitted in the 1st year via all supernumerary quotas'
                    ];
                    foreach($rows4a as $key => $label): ?>
                    <tr>
                        <td><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td>
                            <input aria-label="<?= $key . ' ' . $col ?>" type="number" id="t4a_<?= $key ?>_<?= $col ?>" class="t4a-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="td-total">
                        <td>Total number of students admitted in the program (N1 + N2 + N3 + N4) - excluding those admitted through multiple entry and exit points.</td>
                        <?php foreach($columns as $col): ?>
                        <td class="cell-center" id="t4a_total_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <details class="nba-example-details">
            <summary><i class="fas fa-info-circle"></i> View Table 4A Definitions & Reference Example</summary>
            <p style="margin-top: 8px; line-height: 1.5;">
                <strong>CAY:</strong> Current Academic Year | <strong>CAYm1:</strong> Current Assessment Year | <strong>CAYm2:</strong> Assessment Year Minus 1<br>
                <strong>LYG:</strong> Last Year Graduate | <strong>LYGm1:</strong> LYG Minus 1 | <strong>LYGm2:</strong> LYG Minus 2
            </p>
            <p style="margin-top: 5px; font-style: italic;">Reference Example: Sanctioned N=120, N1=120, N2=10 lateral entry yields Total=130 for cohort accounting.</p>
        </details>
    </div>

    <!-- Table 4B -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4B: Admission details for the program through multiple entry and exit points.</h3>
        <div style="overflow-x: auto;">
            <table class="nba-table">
                <thead>
                    <tr>
                        <th scope="col" class="cell-left" style="width: 30%;">Item (No. of students admitted/exited through multiple entry and exit points) in the respective batch</th>
                        <th scope="col" class="cell-center">CAY</th>
                        <th scope="col" class="cell-center">CAYm1</th>
                        <th scope="col" class="cell-center">CAYm2</th>
                        <th scope="col" class="cell-center">CAYm3</th>
                        <th scope="col" class="cell-center">CAYm4 (LYG)</th>
                        <th scope="col" class="cell-center">CAYm5 (LYGm1)</th>
                        <th scope="col" class="cell-center">CAYm6 (LYGm2)</th>
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
                    <tr><td colspan="8" class="td-shaded font-bold">N5 (Multiple entry)</td></tr>
                    <?php foreach($rows4b_entry as $key => $label): ?>
                    <tr>
                        <td><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td>
                            <input aria-label="<?= $key . ' ' . $col ?>" type="number" id="t4b_<?= $key ?>_<?= $col ?>" class="t4b-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="td-total">
                        <td>N5 = N52 + N53 + N54</td>
                        <?php foreach($columns as $col): ?>
                        <td class="cell-center" id="t4b_total_N5_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>

                    <tr><td colspan="8" class="td-shaded font-bold">N6 (Multiple exit)</td></tr>
                    <?php foreach($rows4b_exit as $key => $label): ?>
                    <tr>
                        <td><?= $label ?></td>
                        <?php foreach($columns as $col): ?>
                        <td>
                            <input aria-label="<?= $key . ' ' . $col ?>" type="number" id="t4b_<?= $key ?>_<?= $col ?>" class="t4b-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="td-total">
                        <td>N6 = N61 + N62 + N63</td>
                        <?php foreach($columns as $col): ?>
                        <td class="cell-center" id="t4b_total_N6_<?= $col ?>">0</td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <details class="nba-example-details">
            <summary><i class="fas fa-info-circle"></i> View Table 4B Reference Example</summary>
            <p style="margin-top: 8px;">Breakdown of N5 (Entry) and N6 (Exit) across lateral and NEP flexible pathways.</p>
        </details>
    </div>

    <!-- Table 4C -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>Table No. 4C: No. of students graduated within the stipulated period of the program.</h3>
        <div style="overflow-x: auto;">
            <table class="nba-table">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2" class="cell-center">Year of entry</th>
                        <th scope="col" rowspan="2" class="cell-center" style="width: 30%;">Total no. of students (N1 + N2 + N3 + N4 + N5 - N6)</th>
                        <th scope="col" colspan="4" class="cell-center">Number of students who have successfully graduated in stipulated period [Total of with + without Backlogs]</th>
                    </tr>
                    <tr>
                        <th scope="col" class="cell-center">I Year</th>
                        <th scope="col" class="cell-center">II Year</th>
                        <th scope="col" class="cell-center">III Year</th>
                        <th scope="col" class="cell-center">IV Year</th>
                    </tr>
                </thead>
                <tbody id="table4c-body">
                    <?php 
                    $rows4c = [
                        'cay'   => 'CAY',
                        'caym1' => 'CAYm1',
                        'caym2' => 'CAYm2',
                        'caym3' => 'CAYm3',
                        'caym4' => 'CAYm4 (LYG)',
                        'caym5' => 'CAYm5 (LYGm1)',
                        'caym6' => 'CAYm6 (LYGm2)'
                    ];
                    $greyed = [
                        'cay'   => [1,2,3,4],
                        'caym1' => [2,3,4],
                        'caym2' => [3,4],
                        'caym3' => [4],
                        'caym4' => [],
                        'caym5' => [],
                        'caym6' => []
                    ];
                    foreach($rows4c as $rKey => $label): ?>
                    <tr>
                        <td class="cell-center font-bold"><?= $label ?></td>
                        <td class="cell-center font-bold" id="t4c_total_<?= $rKey ?>">0</td>
                        <?php for($i=1; $i<=4; $i++): ?>
                        <td class="cell-center <?= in_array($i, $greyed[$rKey]) ? 'td-shaded' : '' ?>">
                            <?php if(!in_array($i, $greyed[$rKey])): ?>
                                <input aria-label="<?= $rKey . ' Year ' . $i ?>" type="number" id="t4c_<?= $rKey ?>_year<?= $i ?>" class="t4c-input cell-center" onchange="saveCurrentLevel()">
                            <?php endif; ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4.1 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.1 Enrolment Ratio (20)</h3>
        <p style="font-weight: bold; margin-bottom: 10px;">ER Points = Average ER * 100</p>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.1.1: Student enrolment ratio in the 1st year.</p>
            <table class="nba-table">
                <thead class="th-highlight">
                    <tr>
                        <th scope="col" style="width: 40%;">Item (Students enrolled in 1st Year over 3 academic years)</th>
                        <th scope="col" class="cell-center">CAY</th>
                        <th scope="col" class="cell-center">CAYm1</th>
                        <th scope="col" class="cell-center">CAYm2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>N= Sanctioned intake in 1st year (AICTE/Competent authority)</td>
                        <td class="cell-center td-shaded" id="t411_N_cay">0</td>
                        <td class="cell-center td-shaded" id="t411_N_caym1">0</td>
                        <td class="cell-center td-shaded" id="t411_N_caym2">0</td>
                    </tr>
                    <tr>
                        <td>N1= Total admitted in 1st year minus migrated out plus migrated in</td>
                        <td class="cell-center td-shaded" id="t411_N1_cay">0</td>
                        <td class="cell-center td-shaded" id="t411_N1_caym1">0</td>
                        <td class="cell-center td-shaded" id="t411_N1_caym2">0</td>
                    </tr>
                    <tr>
                        <td>N4= Total admitted in 1st year via supernumerary quotas</td>
                        <td class="cell-center td-shaded" id="t411_N4_cay">0</td>
                        <td class="cell-center td-shaded" id="t411_N4_caym1">0</td>
                        <td class="cell-center td-shaded" id="t411_N4_caym2">0</td>
                    </tr>
                    <tr class="td-total">
                        <td>Enrolment Ratio (ER) = (N1 + N4) / N</td>
                        <td class="cell-center" id="t411_ER_cay">0.00</td>
                        <td class="cell-center" id="t411_ER_caym1">0.00</td>
                        <td class="cell-center" id="t411_ER_caym2">0.00</td>
                    </tr>
                    <tr class="td-shaded font-bold">
                        <td>Average ER = (ER_1 + ER_2 + ER_3) / 3</td>
                        <td colspan="3" class="cell-center" id="t411_Avg_ER">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4.2 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.2. Success Rate of the Students in the Stipulated Period of the Program (15)</h3>
        <p style="font-weight: bold; margin-bottom: 10px;">SR Points = 1.5 * Average SR / 10</p>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; text-align: center;">Table No. 4.2.1: Success rate in stipulated period.</p>
            <table class="nba-table">
                <thead class="th-highlight">
                    <tr>
                        <th scope="col" style="width: 40%;">Item</th>
                        <th scope="col" class="cell-center">LYG</th>
                        <th scope="col" class="cell-center">LYGm1</th>
                        <th scope="col" class="cell-center">LYGm2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>A*= Admitted in batch (N1+N2+N3+N5-N6 adjusted for lateral threshold)</td>
                        <td class="cell-center td-shaded" id="t421_A_caym4">0</td>
                        <td class="cell-center td-shaded" id="t421_A_caym5">0</td>
                        <td class="cell-center td-shaded" id="t421_A_caym6">0</td>
                    </tr>
                    <tr>
                        <td>B= Graduated from program in stipulated duration</td>
                        <td class="cell-center td-shaded" id="t421_B_caym4">0</td>
                        <td class="cell-center td-shaded" id="t421_B_caym5">0</td>
                        <td class="cell-center td-shaded" id="t421_B_caym6">0</td>
                    </tr>
                    <tr>
                        <td>Success Rate (SR) = (B / A) * 100</td>
                        <td class="cell-center" id="t421_SR_caym4">0.00</td>
                        <td class="cell-center" id="t421_SR_caym5">0.00</td>
                        <td class="cell-center" id="t421_SR_caym6">0.00</td>
                    </tr>
                    <tr class="td-total">
                        <td>Average SR of three batches ((SR_1 + SR_2 + SR_3) / 3)</td>
                        <td colspan="3" class="cell-center" id="t421_Avg_SR">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sections 4.3, 4.4, 4.5 Consolidated Template -->
    <?php
    $academic_perf_sections = [
        ['num' => '4.3', 'prefix' => 't431', 'marks' => '10', 'title' => 'First-Year Students', 'desc' => 'Mean 1st Year GPA/percentage of successful students'],
        ['num' => '4.4', 'prefix' => 't441', 'marks' => '10', 'title' => 'Second-Year Students', 'desc' => 'Mean 2nd Year GPA/percentage of successful students'],
        ['num' => '4.5', 'prefix' => 't451', 'marks' => '10', 'title' => 'Third-Year Students', 'desc' => 'Mean 3rd Year GPA/percentage of successful students'],
    ];
    foreach ($academic_perf_sections as $sec): 
        $p = $sec['prefix'];
    ?>
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3><?= $sec['num'] ?>. Academic Performance of the <?= $sec['title'] ?> (<?= $sec['marks'] ?>)</h3>
        <p style="font-size: 0.9rem; color: #495057; margin-bottom: 10px;">
            API = (X * Y) / Z, where X = Mean GPA, Y = Successful students, Z = Appeared students.
        </p>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table class="nba-table">
                <thead class="th-highlight">
                    <tr>
                        <th scope="col" style="width: 40%;">Academic Performance Metric</th>
                        <th scope="col" class="cell-center">CAYm1</th>
                        <th scope="col" class="cell-center">CAYm2</th>
                        <th scope="col" class="cell-center">CAYm3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>X = <?= $sec['desc'] ?> (10-point scale)</td>
                        <?php foreach(['caym1', 'caym2', 'caym3'] as $c): ?>
                        <td><input aria-label="X <?= $c ?>" type="number" id="<?= $p ?>_X_<?= $c ?>" class="<?= $p ?>-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()" step="0.01"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Y = Total number of successful students</td>
                        <?php foreach(['caym1', 'caym2', 'caym3'] as $c): ?>
                        <td><input aria-label="Y <?= $c ?>" type="number" id="<?= $p ?>_Y_<?= $c ?>" class="<?= $p ?>-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Z = Total number of students appeared in the examination</td>
                        <?php foreach(['caym1', 'caym2', 'caym3'] as $c): ?>
                        <td><input aria-label="Z <?= $c ?>" type="number" id="<?= $p ?>_Z_<?= $c ?>" class="<?= $p ?>-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Academic Performance Index (API) = X * (Y / Z)</td>
                        <?php foreach(['caym1', 'caym2', 'caym3'] as $c): ?>
                        <td class="cell-center font-bold" id="<?= $p ?>_API_<?= $c ?>">0.00</td>
                        <?php endforeach; ?>
                    </tr>
                    <tr class="td-total">
                        <td>Average API = (API_1 + API_2 + API_3) / 3</td>
                        <td colspan="3" class="cell-center" id="<?= $p ?>_Avg_API">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Section 4.6 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.6. Placement, Higher Studies and Entrepreneurship (30)</h3>
        <p style="font-weight: bold; margin-bottom: 10px;">Assessment Points = 30 * Average Placement Index</p>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table class="nba-table">
                <thead class="th-highlight">
                    <tr>
                        <th scope="col" style="width: 40%;">Item</th>
                        <th scope="col" class="cell-center">LYG (CAYm4)</th>
                        <th scope="col" class="cell-center">LYGm1 (CAYm5)</th>
                        <th scope="col" class="cell-center">LYGm2 (CAYm6)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>FS = Final year students admitted in that batch (N1 + N2)</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td><input aria-label="FS <?= $c ?>" type="number" id="t461_FS_<?= $c ?>" class="t461-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>X = Number of students placed in companies or Government sector</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td><input aria-label="X <?= $c ?>" type="number" id="t461_X_<?= $c ?>" class="t461-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Y = Number of students admitted to higher studies with qualifying exam</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td><input aria-label="Y <?= $c ?>" type="number" id="t461_Y_<?= $c ?>" class="t461-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Z = Number of students turned entrepreneurs in engineering field</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td><input aria-label="Z <?= $c ?>" type="number" id="t461_Z_<?= $c ?>" class="t461-input cell-center" onchange="saveCurrentLevel()" oninput="calculateAllTotals()"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Total = X + Y + Z</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td class="cell-center font-bold" id="t461_Sum_<?= $c ?>">0</td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Placement Index (P) = (X + Y + Z) / FS</td>
                        <?php foreach(['caym4', 'caym5', 'caym6'] as $c): ?>
                        <td class="cell-center font-bold" id="t461_P_<?= $c ?>">0.00</td>
                        <?php endforeach; ?>
                    </tr>
                    <tr class="td-total">
                        <td>Average Placement Index = (P_1 + P_2 + P_3) / 3</td>
                        <td colspan="3" class="cell-center" id="t461_Avg_P">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4.7 -->
    <div class="section-card" style="margin-bottom: 2rem;">
        <h3>4.7. Professional Activities (25)</h3>
        
        <h4>4.7.1. Professional Societies/Bodies, Chapters, Clubs & Events Organized (05)</h4>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                <p style="font-weight: bold; margin: 0;">Table No. 4.7.1.1: List of active professional societies/bodies/chapters/clubs.</p>
                <button type="button" onclick="addT4711Row()" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
            </div>
            <table class="nba-table">
                <thead class="th-highlight">
                    <tr>
                        <th scope="col" class="cell-center" style="width: 10%;">S.N.</th>
                        <th scope="col">Name of Professional Societies/Bodies, Chapters, Clubs</th>
                        <th scope="col" class="cell-center" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody id="t4711-tbody"></tbody>
            </table>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px;">Table No. 4.7.1.2: List of events/programs organized.</p>
            <?php foreach($years3 as $yk => $yl): ?>
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 8px 12px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span><?= $yl ?></span>
                    <button type="button" onclick="addT4712Row('<?= $yk ?>')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table class="nba-table" style="margin-top: 0;">
                    <thead class="th-highlight">
                        <tr>
                            <th scope="col" class="cell-center" style="width: 5%;">S.N.</th>
                            <th scope="col">Societies/Bodies/Chapters/Clubs</th>
                            <th scope="col">Event Name</th>
                            <th scope="col">Level</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="cell-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4712-<?= $yk ?>-tbody"></tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>

        <h4 style="margin-top: 2rem;">4.7.2. Students' Participation in Professional Events (10)</h4>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <?php foreach($years3 as $yk => $yl): ?>
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 8px 12px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span><?= $yl ?></span>
                    <button type="button" onclick="addT4721Row('<?= $yk ?>')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table class="nba-table" style="margin-top: 0;">
                    <thead class="th-highlight">
                        <tr>
                            <th scope="col" class="cell-center" style="width: 5%;">S.N.</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Event Name</th>
                            <th scope="col">Level</th>
                            <th scope="col">Date</th>
                            <th scope="col">Award</th>
                            <th scope="col" class="cell-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4721-<?= $yk ?>-tbody"></tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>

        <h4 style="margin-top: 2rem;">4.7.3. Publications in Student Chapters / Technical Magazines (05)</h4>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <?php foreach($years3 as $yk => $yl): ?>
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 8px 12px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span><?= $yl ?></span>
                    <button type="button" onclick="addT4731Row('<?= $yk ?>')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table class="nba-table" style="margin-top: 0;">
                    <thead class="th-highlight">
                        <tr>
                            <th scope="col" class="cell-center" style="width: 5%;">S.N.</th>
                            <th scope="col">Journal/Newsletter Name</th>
                            <th scope="col">Editor</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Issues</th>
                            <th scope="col">Copy Type</th>
                            <th scope="col" class="cell-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4731-<?= $yk ?>-tbody"></tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>

        <h4 style="margin-top: 2rem;">4.7.4. Awards Won in Technical Competitions (05)</h4>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <?php foreach($years3 as $yk => $yl): ?>
            <div style="margin-top: 15px; border: 1px solid #dee2e6;">
                <div style="background: #f8f9fa; padding: 8px 12px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span><?= $yl ?></span>
                    <button type="button" onclick="addT4741Row('<?= $yk ?>')" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">+ Add Row</button>
                </div>
                <table class="nba-table" style="margin-top: 0;">
                    <thead class="th-highlight">
                        <tr>
                            <th scope="col" class="cell-center" style="width: 5%;">S.N.</th>
                            <th scope="col">Student & Semester</th>
                            <th scope="col">Publisher</th>
                            <th scope="col">Journal / Conference</th>
                            <th scope="col">Volume & Issue</th>
                            <th scope="col">Award</th>
                            <th scope="col" class="cell-center" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="t4741-<?= $yk ?>-tbody"></tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php 
    $crit_id = 4;
    $module = 'nba';
    $save_onclick = 'saveData()';
    include __DIR__ . '/../partials/accreditation_action_bar.php'; 
    ?>
</div>

<script>
window.NBA_CONFIG = {
    deptId: <?= json_encode((string)$dept_id) ?>,
    year: <?= json_encode((string)$year) ?>,
    csrfToken: <?= json_encode(csrfToken()) ?>,
    baseUrl: <?= json_encode(BASE_URL) ?>,
    initialData: <?= json_encode($criteria_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
};
</script>
<script src="<?= BASE_URL ?>/assets/js/nba_criterion4.js"></script>
</body>
</html>
