<?php
$criterion_num = 5;
$criterion_title = "Faculty Information (100)";
include __DIR__ . '/../partials/nba_header.php';
?>
    <form id="nbaForm">
        <!-- Table 5A -->
        <div class="section-card">
            <h3>Table No. 5A: Faculty details</h3>
            <div class="section-desc">
                <p style="color: #0d6efd; font-style: italic;">Note: This data is automatically fetched directly from the database based on the faculty profiles associated with this department.</p>
            </div>
            <?php
            $faculty_data = [];
            if (isset($dept_id) && $dept_id > 0) {
                global $conn;
                $sql = "
                    SELECT u.full_name, up.* 
                    FROM users u
                    JOIN user_roles ur ON u.user_id = ur.user_id
                    LEFT JOIN user_profiles up ON u.user_id = up.user_id
                    WHERE ur.dept_id = ?
                    GROUP BY u.user_id ORDER BY u.full_name ASC
                ";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $dept_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_assoc()) { $faculty_data[] = $row; }
                    $stmt->close();
                }
            }
            ?>
            <div style="overflow-x: auto; margin-bottom: 20px;">
                <table class="nba-table" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;" border="1">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col" style="min-width: 50px;">S.N.</th>
                            <th scope="col" style="min-width: 150px;">Name of the Faculty</th>
                            <th scope="col" style="min-width: 100px;">PAN No.</th>
                            <th scope="col" style="min-width: 120px;">APAAR faculty ID* (if any)</th>
                            <th scope="col" style="min-width: 100px;">Highest degree</th>
                            <th scope="col" style="min-width: 120px;">University</th>
                            <th scope="col" style="min-width: 150px;">Area of Specialization</th>
                            <th scope="col" style="min-width: 100px;">Date of Joining in this Institution</th>
                            <th scope="col" style="min-width: 100px;">Experience in years in current institute</th>
                            <th scope="col" style="min-width: 150px;">Designation at Time Joining in this Institution</th>
                            <th scope="col" style="min-width: 150px;">Present Designation</th>
                            <th scope="col" style="min-width: 120px;">The date on which Designated as Professor/ Associate Professor if any</th>
                            <th scope="col" style="min-width: 120px;">Nature of Association (Regular/ Contract/ Ad hoc)</th>
                            <th scope="col" style="min-width: 120px;">If contractual mention Full time or (Part time or hourly based)</th>
                            <th scope="col" style="min-width: 80px;">Currently Associated (Y/N)</th>
                            <th scope="col" style="min-width: 100px;">Date of Leaving if any</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($faculty_data)): ?>
                            <tr><td colspan="16" style="text-align: center; padding: 20px;">No faculty data found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($faculty_data as $index => $fac): ?>
                                <tr>
                                    <td style="text-align: center;"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($fac['full_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['pan_no'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['apaar_id'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($fac['highest_degree'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['university'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['specialization'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['doj_institution'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['experience_years'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['designation_joining'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['designation_present'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['date_designated_prof'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($fac['association_nature'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($fac['contract_type'] ?: '-') ?></td>
                                    <td style="text-align: center;"><?= !empty($fac['is_currently_associated']) ? 'Y' : 'N' ?></td>
                                    <td><?= htmlspecialchars($fac['date_of_leaving'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- 5.1 Student-Faculty Ratio -->
        <div class="section-card">
            <h3>5.1 Student-Faculty Ratio (SFR) (30)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col" style="text-align: left;">Item</th>
                            <th scope="col">CAY</th>
                            <th scope="col">CAYm1</th>
                            <th scope="col">CAYm2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DS = Total no. of students in all UG and PG programs in the Department</td>
                            <td><input aria-label="T51 DS Cay" type="number" id="t51_DS_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 DS Caym1" type="number" id="t51_DS_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 DS Caym2" type="number" id="t51_DS_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>AS = Total no. of students of all UG and PG programs in allied departments</td>
                            <td><input aria-label="T51 AS Cay" type="number" id="t51_AS_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 AS Caym1" type="number" id="t51_AS_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 AS Caym2" type="number" id="t51_AS_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>S = Total no. of students (DS + AS)</td>
                            <td><input aria-label="T51 S Cay" type="text" id="t51_S_cay" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 S Caym1" type="text" id="t51_S_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 S Caym2" type="text" id="t51_S_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr>
                            <td>DF = Total no. of faculty members in the Department</td>
                            <td><input aria-label="T51 DF Cay" type="number" id="t51_DF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 DF Caym1" type="number" id="t51_DF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 DF Caym2" type="number" id="t51_DF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>AF = Total no. of faculty members in the allied Departments</td>
                            <td><input aria-label="T51 AF Cay" type="number" id="t51_AF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 AF Caym1" type="number" id="t51_AF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 AF Caym2" type="number" id="t51_AF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>F = Total no. of faculty members (DF + AF)</td>
                            <td><input aria-label="T51 F Cay" type="text" id="t51_F_cay" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 F Caym1" type="text" id="t51_F_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 F Caym2" type="text" id="t51_F_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr>
                            <td>FF = Faculty members in F who have a 100% teaching load in first-year courses</td>
                            <td><input aria-label="T51 FF Cay" type="number" id="t51_FF_cay" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 FF Caym1" type="number" id="t51_FF_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T51 FF Caym2" type="number" id="t51_FF_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td>Student Faculty Ratio (SFR) = S / (F - FF)</td>
                            <td><input aria-label="T51 SFR Cay" type="text" id="t51_SFR_cay" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 SFR Caym1" type="text" id="t51_SFR_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T51 SFR Caym2" type="text" id="t51_SFR_caym2" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr style="background:#e9ecef;">
                            <td colspan="3" style="text-align:right;"><strong>Average SFR for three years:</strong></td>
                            <td><input aria-label="T51 SFR Avg" type="text" id="t51_SFR_avg" class="calc-box readonly-box" readonly style="font-size:1.1rem; color:#d9534f;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.2 Faculty Qualification -->
        <div class="section-card">
            <h3>5.2 Faculty Qualification (25)</h3>
            <p><strong>FQI = 2.5 x [(10X + 4Y) / RF]</strong> where X = PhD, Y = M.Tech/Masters.</p>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col">Year</th>
                            <th scope="col">X (Ph.D)</th>
                            <th scope="col">Y (Masters)</th>
                            <th scope="col">RF (Required Faculty = S/20)</th>
                            <th scope="col">FQI = 2.5 x [(10X + 4Y)/RF]</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(['cay','caym1','caym2'] as $yr): ?>
                            <tr>
                                <td style="text-align:center; font-weight:bold;"><?= strtoupper($yr) ?></td>
                                <td><input aria-label="Number Input" type="number" id="t52_X_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input aria-label="Number Input" type="number" id="t52_Y_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input aria-label="Number Input" type="number" id="t52_RF_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input aria-label="Text Input" type="text" id="t52_FQI_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background:#e9ecef;">
                            <td colspan="4" style="text-align:right;"><strong>Average FQI:</strong></td>
                            <td><input aria-label="T52 FQI Avg" type="text" id="t52_FQI_avg" class="calc-box readonly-box" readonly style="font-size:1.1rem; color:#d9534f;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.3 Faculty Cadre Proportion -->
        <div class="section-card">
            <h3>5.3 Faculty Cadre Proportion (25)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col" rowspan="2">Year</th>
                            <th scope="col" colspan="2">Professors</th>
                            <th scope="col" colspan="2">Associate Professors</th>
                            <th scope="col" colspan="2">Assistant Professors</th>
                        </tr>
                        <tr>
                            <th scope="col">Required (RF1)</th>
                            <th scope="col">Available (AF1)</th>
                            <th scope="col">Required (RF2)</th>
                            <th scope="col">Available (AF2)</th>
                            <th scope="col">Required (RF3)</th>
                            <th scope="col">Available (AF3)</th>
                        </tr>
                    </thead>
                    <tbody id="cadre-body">
                        <?php $years = ['CAY', 'CAYm1', 'CAYm2']; ?>
                        <?php foreach($years as $yr): ?>
                            <tr>
                                <td style="text-align:center; font-weight:bold;"><?= $yr ?></td>
                                <td><input aria-label="Number Input" type="number" id="t53_prof_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input aria-label="Number Input" type="number" id="t53_prof_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input aria-label="Number Input" type="number" id="t53_asso_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input aria-label="Number Input" type="number" id="t53_asso_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                                <td><input aria-label="Number Input" type="number" id="t53_asst_req_<?= $yr ?>" class="calc-box readonly-box" readonly></td>
                                <td><input aria-label="Number Input" type="number" id="t53_asst_avl_<?= $yr ?>" class="calc-box" oninput="saveCurrentLevel()"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5.4 Visiting/Adjunct Faculty -->
        <div class="section-card">
            <h3>5.4 Visiting/Adjunct Faculty/Professor of Practice (10)</h3>
            <button type="button" class="btn-add" onclick="addVisitingFaculty()">+ Add Visiting Faculty Row</button>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col">S.N.</th>
                            <th scope="col">Year</th>
                            <th scope="col">Name of the Person</th>
                            <th scope="col">Designation & Organization</th>
                            <th scope="col">Name of the Course</th>
                            <th scope="col">No. of hours handled</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="visiting-body">
                        <!-- Populated by JS -->
                    </tbody>
                    <tfoot>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total CAYm1 Hours:</td>
                            <td id="t54_tot_caym1" style="text-align:center;">0</td><td></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total CAYm2 Hours:</td>
                            <td id="t54_tot_caym2" style="text-align:center;">0</td><td></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total CAYm3 Hours:</td>
                            <td id="t54_tot_caym3" style="text-align:center;">0</td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 5.5 Faculty Retention -->
        <div class="section-card">
            <h3>5.5 Faculty Retention (10)</h3>
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="nba-table" border="1" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #fff3cd;">
                        <tr>
                            <th scope="col" style="text-align: left;">Item</th>
                            <th scope="col">CAYm1</th>
                            <th scope="col">CAYm2</th>
                            <th scope="col">CAYm3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>RF = No. of required faculty to adhere to 20:1 ratio</td>
                            <td><input aria-label="T55 RF Caym1" type="number" id="t55_RF_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 RF Caym2" type="number" id="t55_RF_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 RF Caym3" type="number" id="t55_RF_caym3" class="calc-box" oninput="saveCurrentLevel()" placeholder="(Enter manually)"></td>
                        </tr>
                        <tr>
                            <td>AF = The no. of available faculty members in Dept & allied</td>
                            <td><input aria-label="T55 AF Caym1" type="number" id="t55_AF_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 AF Caym2" type="number" id="t55_AF_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 AF Caym3" type="number" id="t55_AF_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>A = Faculty with < 1 year experience</td>
                            <td><input aria-label="T55 A Caym1" type="number" id="t55_A_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 A Caym2" type="number" id="t55_A_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 A Caym3" type="number" id="t55_A_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>B = Faculty with > 1 year and < 2 years experience</td>
                            <td><input aria-label="T55 B Caym1" type="number" id="t55_B_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 B Caym2" type="number" id="t55_B_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 B Caym3" type="number" id="t55_B_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>C = Faculty with > 2 years and < 3 years experience</td>
                            <td><input aria-label="T55 C Caym1" type="number" id="t55_C_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 C Caym2" type="number" id="t55_C_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 C Caym3" type="number" id="t55_C_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>D = Faculty with > 3 years and < 4 years experience</td>
                            <td><input aria-label="T55 D Caym1" type="number" id="t55_D_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 D Caym2" type="number" id="t55_D_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 D Caym3" type="number" id="t55_D_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr>
                            <td>E = Faculty with > 4 years experience</td>
                            <td><input aria-label="T55 E Caym1" type="number" id="t55_E_caym1" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 E Caym2" type="number" id="t55_E_caym2" class="calc-box" oninput="saveCurrentLevel()"></td>
                            <td><input aria-label="T55 E Caym3" type="number" id="t55_E_caym3" class="calc-box" oninput="saveCurrentLevel()"></td>
                        </tr>
                        <tr style="background:#f1f3f5; font-weight:bold;">
                            <td>FR = (((A*0)+(B*1)+(C*2)+(D*3)+(E*4))/RF) * 2.50</td>
                            <td><input aria-label="T55 FR Caym1" type="text" id="t55_FR_caym1" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 FR Caym2" type="text" id="t55_FR_caym2" class="calc-box readonly-box" readonly></td>
                            <td><input aria-label="T55 FR Caym3" type="text" id="t55_FR_caym3" class="calc-box readonly-box" readonly></td>
                        </tr>
                        <tr style="background:#e9ecef; font-weight:bold;">
                            <td colspan="3" style="text-align:right;">Average FR (Limited to 10):</td>
                            <td><input aria-label="T55 FR Avg" type="text" id="t55_FR_avg" class="calc-box readonly-box" readonly style="color:#d9534f; font-size:1.1rem;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php 
        $crit_id = 5;
        $module = 'nba';
        $save_onclick = 'saveCriterionData()';
        include __DIR__ . '/../partials/accreditation_action_bar.php'; 
        ?>

    </form>
</div>

<script>
window.NBA_CONFIG = {
    deptId: <?= (int)($dept_id ?? 0) ?>,
    year: <?= json_encode((string)($year ?? '')) ?>,
    initialData: <?= json_encode($criteria_data ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
};
</script>
<script src="<?= BASE_URL ?>/assets/js/nba_criterion5.js"></script>
</body>
</html>

