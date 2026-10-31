<?php
/**
 * Dynamic Workflow Management View
 * 
 * Interactive workflow pipeline builder and document category mapping:
 * - Visual multi-step approval pipelines
 * - Step configuration with roles & scopes
 * - Document type to workflow routing matrix
 */
$page_title = "Workflow Management | GMRIT FMS";
require_once __DIR__ . '/../../../includes/header.php';

$active_tab = isset($_GET['tab']) && $_GET['tab'] === 'mappings' ? 'mappings' : 'pipelines';
?>

<div class="main-container" style="max-width: 1400px; margin: 30px auto; padding: 0 20px;">
    
    <!-- Top Header Banner -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px 28px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">State Machine Engine</span>
                <span style="background: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px;">Institutional Governance</span>
            </div>
            <h1 style="margin: 0; font-size: 1.65rem; color: #0f172a; font-weight: 800; letter-spacing: -0.5px;">Dynamic Workflow Management</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.92rem;">
                Configure approval pipelines, define multi-tier review steps, and govern how faculty and departmental records flow across the institution.
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="button" onclick="openCreateWorkflowModal()" style="background: #0284c7; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.92rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2); transition: background 0.2s;">
                <i class="fas fa-plus-circle"></i> Create New Workflow
            </button>
            <a href="<?= BASE_URL ?>/public/index.php?route=dashboard" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.92rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($success_msg)): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.1rem;"></i>
                <span><?= htmlspecialchars($success_msg) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 1.1rem;"></i>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px;">
        <button type="button" onclick="switchTab('pipelines')" id="tab-btn-pipelines" style="background: none; border: none; border-bottom: 3px solid <?= $active_tab === 'pipelines' ? '#0284c7' : 'transparent' ?>; color: <?= $active_tab === 'pipelines' ? '#0284c7' : '#64748b' ?>; font-weight: 700; font-size: 0.98rem; padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-bottom: -2px;">
            <i class="fas fa-project-diagram"></i>
            Approval Pipelines
            <span style="background: <?= $active_tab === 'pipelines' ? '#e0f2fe' : '#f1f5f9' ?>; color: <?= $active_tab === 'pipelines' ? '#0369a1' : '#64748b' ?>; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px;">
                <?= count($workflows) ?>
            </span>
        </button>
        <button type="button" onclick="switchTab('mappings')" id="tab-btn-mappings" style="background: none; border: none; border-bottom: 3px solid <?= $active_tab === 'mappings' ? '#0284c7' : 'transparent' ?>; color: <?= $active_tab === 'mappings' ? '#0284c7' : '#64748b' ?>; font-weight: 700; font-size: 0.98rem; padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-bottom: -2px;">
            <i class="fas fa-exchange-alt"></i>
            Document Category Mapping
            <span style="background: <?= $active_tab === 'mappings' ? '#e0f2fe' : '#f1f5f9' ?>; color: <?= $active_tab === 'mappings' ? '#0369a1' : '#64748b' ?>; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px;">
                <?= count($doc_types) ?>
            </span>
        </button>
    </div>

    <!-- TAB 1: WORKFLOW PIPELINES -->
    <div id="tab-content-pipelines" style="display: <?= $active_tab === 'pipelines' ? 'block' : 'none' ?>;">
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($workflows as $wf): ?>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 6px 16px rgba(0,0,0,0.06)';" onmouseout="this.style.boxShadow='0 2px 5px rgba(0,0,0,0.02)';">
                    
                    <!-- Workflow Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                <h2 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #0f172a;">
                                    <?= htmlspecialchars($wf['label']) ?>
                                </h2>
                                <span style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; font-family: monospace; font-size: 0.8rem; padding: 2px 8px; border-radius: 4px;">
                                    <?= htmlspecialchars($wf['workflow_key']) ?>
                                </span>
                                <?php if (in_array($wf['workflow_key'], ['department_standard', 'auto_accept'], true)): ?>
                                    <span style="background: #fef3c7; color: #92400e; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                        Core System
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p style="margin: 0; color: #64748b; font-size: 0.88rem; max-width: 800px;">
                                <?= htmlspecialchars($wf['description'] ?: 'No description provided.') ?>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button type="button" onclick="openConfigureStepsModal(<?= htmlspecialchars(json_encode($wf), ENT_QUOTES, 'UTF-8') ?>)" style="background: #0284c7; color: white; border: none; padding: 7px 14px; border-radius: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-sliders-h"></i> Configure Steps
                            </button>
                            <button type="button" onclick="openEditWorkflowModal(<?= htmlspecialchars(json_encode($wf), ENT_QUOTES, 'UTF-8') ?>)" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 7px 12px; border-radius: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if (!in_array($wf['workflow_key'], ['department_standard', 'auto_accept'], true)): ?>
                                <button type="button" onclick="confirmDeleteWorkflow(<?= (int)$wf['workflow_id'] ?>, '<?= htmlspecialchars($wf['label'], ENT_QUOTES, 'UTF-8') ?>')" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 7px 12px; border-radius: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pipeline Statistics Badges -->
                    <div style="display: flex; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
                        <span style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.82rem; font-weight: 600; padding: 3px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-layer-group"></i> <?= (int)$wf['step_count'] ?> Approval Stage<?= (int)$wf['step_count'] !== 1 ? 's' : '' ?>
                        </span>
                        <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.82rem; font-weight: 600; padding: 3px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-file-alt"></i> <?= (int)$wf['doc_type_count'] ?> Document Categor<?= (int)$wf['doc_type_count'] === 1 ? 'y' : 'ies' ?> Assigned
                        </span>
                    </div>

                    <!-- Visual Pipeline Flowchart -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; overflow-x: auto;">
                        <div style="display: flex; align-items: center; gap: 12px; min-width: 650px;">
                            
                            <!-- Start Node -->
                            <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 130px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b;">Stage 0</div>
                                <div style="font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-top: 2px;">Faculty / Student</div>
                                <div style="font-size: 0.75rem; color: #0284c7; margin-top: 2px;"><i class="fas fa-cloud-upload-alt"></i> Upload</div>
                            </div>

                            <!-- Arrow -->
                            <div style="display: flex; align-items: center; color: #94a3b8;"><i class="fas fa-arrow-right"></i></div>

                            <?php if (empty($wf['steps'])): ?>
                                <!-- Zero Steps Auto Accept -->
                                <div style="background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 8px; padding: 10px 16px; text-align: center; min-width: 200px;">
                                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #047857;">Instant Acceptance</div>
                                    <div style="font-size: 0.9rem; font-weight: 800; color: #065f46; margin-top: 2px;">Zero Review Steps</div>
                                    <div style="font-size: 0.75rem; color: #059669; margin-top: 2px;">Auto-Accepted immediately</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($wf['steps'] as $s_idx => $step): ?>
                                    <!-- Step Node -->
                                    <div style="background: #ffffff; border: 1.5px solid #0284c7; border-radius: 8px; padding: 10px 16px; text-align: center; min-width: 170px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.08); position: relative;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                            <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #0369a1;">
                                                Step <?= (int)$step['step_order'] ?>
                                            </span>
                                            <span style="background: <?= $step['scope'] === 'department' ? '#eff6ff' : '#fdf2f8' ?>; color: <?= $step['scope'] === 'department' ? '#1d4ed8' : '#be185d' ?>; font-size: 0.68rem; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: capitalize;">
                                                <?= htmlspecialchars($step['scope']) ?>
                                            </span>
                                        </div>
                                        <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                                            <?= htmlspecialchars($step['step_label']) ?>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #475569; margin-top: 3px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                            <i class="fas fa-user-shield" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($step['role_name']) ?>
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <div style="display: flex; align-items: center; color: #94a3b8;"><i class="fas fa-arrow-right"></i></div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- End Node -->
                            <div style="background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 120px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #047857;">Final State</div>
                                <div style="font-size: 0.88rem; font-weight: 800; color: #065f46; margin-top: 2px;">Accepted</div>
                                <div style="font-size: 0.75rem; color: #10b981; margin-top: 2px;"><i class="fas fa-check-circle"></i> Institutional Record</div>
                            </div>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TAB 2: DOCUMENT CATEGORY MAPPING -->
    <div id="tab-content-mappings" style="display: <?= $active_tab === 'mappings' ? 'block' : 'none' ?>;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); overflow: hidden;">
            
            <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div>
                    <h2 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f172a;">Document Category &rarr; Workflow Routing</h2>
                    <p style="margin: 3px 0 0; color: #64748b; font-size: 0.86rem;">
                        Choose which approval pipeline is triggered when a faculty or student uploads a specific document type.
                    </p>
                </div>
                <div>
                    <input type="text" id="docTypeSearchInput" onkeyup="filterDocTypesTable()" placeholder="Search category..." style="padding: 8px 14px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; outline: none; width: 240px;">
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table id="docTypesTable" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 12px 20px; width: 70px;">ID</th>
                            <th style="padding: 12px 20px;">Document Category</th>
                            <th style="padding: 12px 20px; width: 180px;">Category Code</th>
                            <th style="padding: 12px 20px;">Current Assigned Pipeline</th>
                            <th style="padding: 12px 20px; text-align: right; width: 280px;">Reassign Workflow</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doc_types as $dt): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                                <td style="padding: 14px 20px; font-weight: 700; color: #64748b;">
                                    #<?= (int)$dt['type_id'] ?>
                                </td>
                                <td style="padding: 14px 20px; font-weight: 600; color: #0f172a;">
                                    <?= htmlspecialchars($dt['label']) ?>
                                </td>
                                <td style="padding: 14px 20px; color: #64748b; font-family: monospace; font-size: 0.82rem;">
                                    <?= htmlspecialchars($dt['type_code'] ?? '—') ?>
                                </td>
                                <td style="padding: 14px 20px;">
                                    <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.82rem; font-weight: 600; padding: 3px 10px; border-radius: 6px;">
                                        <i class="fas fa-project-diagram me-1"></i> <?= htmlspecialchars($dt['workflow_label'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/workflows/assign_type" style="display: inline-flex; align-items: center; gap: 8px;">
                                        <input type="hidden" name="_csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="type_id" value="<?= (int)$dt['type_id'] ?>">
                                        <select name="workflow_id" style="padding: 6px 10px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                                            <?php foreach ($workflows as $wf_opt): ?>
                                                <option value="<?= (int)$wf_opt['workflow_id'] ?>" <?= ((int)$wf_opt['workflow_id'] === (int)$dt['workflow_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($wf_opt['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" style="background: #0284c7; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; cursor: pointer;">
                                            Save
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- ============================================================
     MODAL 1: CONFIGURE STEPS MODAL
============================================================ -->
<div id="stepsConfigModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; overflow-y: auto; padding: 40px 20px;">
    <div style="background: white; border-radius: 12px; max-width: 780px; margin: 0 auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        
        <div style="background: #0f172a; color: white; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="stepsModalTitle" style="margin: 0; font-size: 1.2rem; font-weight: 700;">Configure Approval Pipeline</h3>
                <p id="stepsModalSubtitle" style="margin: 3px 0 0; color: #94a3b8; font-size: 0.84rem;">Adjust review stages, roles, and scope rules.</p>
            </div>
            <button type="button" onclick="closeConfigureStepsModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/workflows/save_steps" style="padding: 24px;">
            <input type="hidden" name="_csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="workflow_id" id="stepsWorkflowId" value="0">

            <div style="background: #f0f9ff; border-left: 4px solid #0284c7; color: #0369a1; padding: 12px 16px; border-radius: 6px; font-size: 0.86rem; margin-bottom: 20px; line-height: 1.45;">
                <i class="fas fa-info-circle me-1"></i> <strong>Automated Deterministic Transitions:</strong> Approval at any stage automatically advances the document to the next step (or marks it Accepted at the final step). Rejections stop the document, and resubmissions automatically return it to Step 1.
            </div>

            <div id="stepRowsContainer" style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                <!-- Dynamically populated via JS -->
            </div>

            <button type="button" onclick="addNewStepRow()" style="background: #f1f5f9; color: #0f172a; border: 1.5px dashed #94a3b8; padding: 10px; width: 100%; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 24px; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                <i class="fas fa-plus"></i> Add New Approval Stage
            </button>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
                <button type="button" onclick="closeConfigureStepsModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: #0284c7; color: white; border: none; padding: 9px 22px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                    Save Pipeline Steps
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================
     MODAL 2: CREATE / EDIT WORKFLOW MODAL
============================================================ -->
<div id="workflowModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; overflow-y: auto; padding: 40px 20px;">
    <div style="background: white; border-radius: 12px; max-width: 550px; margin: 0 auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        
        <div style="background: #0f172a; color: white; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="workflowModalTitle" style="margin: 0; font-size: 1.2rem; font-weight: 700;">Create Workflow</h3>
            <button type="button" onclick="closeWorkflowModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/workflows/save" style="padding: 24px;">
            <input type="hidden" name="_csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="workflow_id" id="wfModalId" value="0">

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #1e293b; margin-bottom: 6px;">Workflow Label *</label>
                <input type="text" name="label" id="wfModalLabel" required placeholder="e.g. Fast-Track Department Review" style="width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #1e293b; margin-bottom: 6px;">Unique Identifier Key</label>
                <input type="text" name="workflow_key" id="wfModalKey" placeholder="e.g. fast_track_review (auto-generated if empty)" style="width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; outline: none; font-family: monospace;">
                <div style="font-size: 0.76rem; color: #64748b; margin-top: 4px;">Use lowercase letters, numbers, and underscores only.</div>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; color: #1e293b; margin-bottom: 6px;">Description</label>
                <textarea name="description" id="wfModalDesc" rows="3" placeholder="Brief description of when this workflow should be used..." style="width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.92rem; outline: none; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
                <button type="button" onclick="closeWorkflowModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: #0284c7; color: white; border: none; padding: 9px 22px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                    Save Workflow
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================
     MODAL 3: DELETE CONFIRMATION MODAL
============================================================ -->
<div id="deleteWorkflowModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; overflow-y: auto; padding: 40px 20px;">
    <div style="background: white; border-radius: 12px; max-width: 480px; margin: 40px auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background: #fee2e2; border-bottom: 1px solid #fca5a5; padding: 18px 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.4rem;"></i>
            <h3 style="margin: 0; color: #991b1b; font-size: 1.15rem; font-weight: 700;">Delete Workflow</h3>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/public/index.php?route=admin/workflows/delete" style="padding: 24px;">
            <input type="hidden" name="_csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="workflow_id" id="deleteWorkflowId" value="0">

            <p style="margin: 0 0 16px; color: #334155; font-size: 0.92rem; line-height: 1.5;">
                Are you sure you want to permanently delete workflow <strong id="deleteWorkflowLabel" style="color: #0f172a;"></strong>?
            </p>
            <div style="background: #fff1f2; border: 1px solid #fecdd3; padding: 10px 14px; border-radius: 6px; color: #be123c; font-size: 0.82rem; margin-bottom: 20px;">
                <i class="fas fa-shield-alt me-1"></i> Workflows that still have document categories mapped or documents pending review cannot be deleted.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteWorkflowModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: #dc2626; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Confirm Delete
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Available Roles JSON for JavaScript rendering -->
<script>
const AVAILABLE_ROLES = <?= json_encode($roles, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function switchTab(tab) {
    document.getElementById('tab-content-pipelines').style.display = tab === 'pipelines' ? 'block' : 'none';
    document.getElementById('tab-content-mappings').style.display = tab === 'mappings' ? 'block' : 'none';

    document.getElementById('tab-btn-pipelines').style.borderBottomColor = tab === 'pipelines' ? '#0284c7' : 'transparent';
    document.getElementById('tab-btn-pipelines').style.color = tab === 'pipelines' ? '#0284c7' : '#64748b';
    document.getElementById('tab-btn-mappings').style.borderBottomColor = tab === 'mappings' ? '#0284c7' : 'transparent';
    document.getElementById('tab-btn-mappings').style.color = tab === 'mappings' ? '#0284c7' : '#64748b';
}

function openCreateWorkflowModal() {
    document.getElementById('workflowModalTitle').textContent = 'Create New Workflow';
    document.getElementById('wfModalId').value = '0';
    document.getElementById('wfModalLabel').value = '';
    document.getElementById('wfModalKey').value = '';
    document.getElementById('wfModalDesc').value = '';
    document.getElementById('workflowModal').style.display = 'block';
}

function openEditWorkflowModal(wf) {
    document.getElementById('workflowModalTitle').textContent = 'Edit Workflow Details';
    document.getElementById('wfModalId').value = wf.workflow_id;
    document.getElementById('wfModalLabel').value = wf.label;
    document.getElementById('wfModalKey').value = wf.workflow_key;
    document.getElementById('wfModalDesc').value = wf.description || '';
    document.getElementById('workflowModal').style.display = 'block';
}

function closeWorkflowModal() {
    document.getElementById('workflowModal').style.display = 'none';
}

function openConfigureStepsModal(wf) {
    document.getElementById('stepsModalTitle').textContent = 'Configure Steps: ' + wf.label;
    document.getElementById('stepsWorkflowId').value = wf.workflow_id;
    
    const container = document.getElementById('stepRowsContainer');
    container.innerHTML = '';

    if (wf.steps && wf.steps.length > 0) {
        wf.steps.forEach((step, idx) => {
            addStepRow(step.step_label, step.responsible_role_id, step.scope);
        });
    } else {
        // Zero steps initially
    }

    document.getElementById('stepsConfigModal').style.display = 'block';
}

function closeConfigureStepsModal() {
    document.getElementById('stepsConfigModal').style.display = 'none';
}

function addStepRow(label = '', roleId = 0, scope = 'department') {
    const container = document.getElementById('stepRowsContainer');
    const stepIndex = container.children.length + 1;

    let rolesOptions = '';
    AVAILABLE_ROLES.forEach(r => {
        const isSel = parseInt(r.role_id) === parseInt(roleId) ? 'selected' : '';
        rolesOptions += `<option value="${r.role_id}" ${isSel}>${r.role_name}</option>`;
    });

    const row = document.createElement('div');
    row.className = 'step-row-item';
    row.style.background = '#f8fafc';
    row.style.border = '1px solid #e2e8f0';
    row.style.borderRadius = '8px';
    row.style.padding = '14px 16px';
    row.style.display = 'flex';
    row.style.alignItems = 'center';
    row.style.gap = '12px';
    row.style.flexWrap = 'wrap';

    row.innerHTML = `
        <div style="background: #0284c7; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem;" class="step-num-badge">
            ${stepIndex}
        </div>
        <div style="flex: 2; min-width: 180px;">
            <label style="display: block; font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px;">Step Label</label>
            <input type="text" name="step_labels[]" value="${escapeHtml(label || 'Stage ' + stepIndex)}" required placeholder="e.g. Dept Coordinator Review" style="width: 100%; box-sizing: border-box; padding: 7px 10px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; outline: none;">
        </div>
        <div style="flex: 1.5; min-width: 140px;">
            <label style="display: block; font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px;">Responsible Role</label>
            <select name="role_ids[]" style="width: 100%; box-sizing: border-box; padding: 7px 10px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; background: white; outline: none;">
                ${rolesOptions}
            </select>
        </div>
        <div style="flex: 1.2; min-width: 120px;">
            <label style="display: block; font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px;">Scope</label>
            <select name="scopes[]" style="width: 100%; box-sizing: border-box; padding: 7px 10px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.88rem; background: white; outline: none;">
                <option value="department" ${scope === 'department' ? 'selected' : ''}>Department</option>
                <option value="global" ${scope === 'global' ? 'selected' : ''}>Global (Campus)</option>
            </select>
        </div>
        <div style="display: flex; gap: 6px; align-items: flex-end; margin-top: 16px;">
            <button type="button" onclick="moveStepRow(this, -1)" title="Move Up" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; cursor: pointer; color: #475569;"><i class="fas fa-chevron-up"></i></button>
            <button type="button" onclick="moveStepRow(this, 1)" title="Move Down" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; cursor: pointer; color: #475569;"><i class="fas fa-chevron-down"></i></button>
            <button type="button" onclick="removeStepRow(this)" title="Remove Step" style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; padding: 6px 10px; cursor: pointer; color: #dc2626;"><i class="fas fa-trash-alt"></i></button>
        </div>
    `;

    container.appendChild(row);
    renumberStepBadges();
}

function addNewStepRow() {
    addStepRow();
}

function removeStepRow(btn) {
    const row = btn.closest('.step-row-item');
    if (row) {
        row.remove();
        renumberStepBadges();
    }
}

function moveStepRow(btn, direction) {
    const row = btn.closest('.step-row-item');
    if (!row) return;

    if (direction === -1 && row.previousElementSibling) {
        row.parentNode.insertBefore(row, row.previousElementSibling);
    } else if (direction === 1 && row.nextElementSibling) {
        row.parentNode.insertBefore(row.nextElementSibling, row);
    }
    renumberStepBadges();
}

function renumberStepBadges() {
    const container = document.getElementById('stepRowsContainer');
    const badges = container.querySelectorAll('.step-num-badge');
    badges.forEach((b, idx) => {
        b.textContent = idx + 1;
    });
}

function confirmDeleteWorkflow(id, label) {
    document.getElementById('deleteWorkflowId').value = id;
    document.getElementById('deleteWorkflowLabel').textContent = label;
    document.getElementById('deleteWorkflowModal').style.display = 'block';
}

function closeDeleteWorkflowModal() {
    document.getElementById('deleteWorkflowModal').style.display = 'none';
}

function filterDocTypesTable() {
    const input = document.getElementById('docTypeSearchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('docTypesTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const text = tr[i].textContent || tr[i].innerText;
        tr[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
    }
}

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return (text || '').replace(/[&<>"']/g, m => map[m]);
}
</script>
