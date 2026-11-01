document.addEventListener('DOMContentLoaded', function() {
    var activityCatEl = document.getElementById('meta_activity_category');
    var eventTypeEl = document.getElementById('meta_event_type');
    var topicDomainEl = document.getElementById('meta_topic_domain');
    var subCatEl = document.getElementById('meta_sub_category');
    var resourcePersonEl = document.getElementById('meta_resource_person');
    var locationEl = document.getElementById('meta_location');
    
    var targetAudienceEl = document.getElementById('meta_target_audience');
    var eventTitleEl = document.getElementById('meta_event_title');
    
    var eventTypeGrp = eventTypeEl ? eventTypeEl.closest('.form-group') : null;
    var topicDomainGrp = topicDomainEl ? topicDomainEl.closest('.form-group') : null;
    var subCatGrp = subCatEl ? subCatEl.closest('.form-group') : null;
    var resourcePersonGrp = resourcePersonEl ? resourcePersonEl.closest('.form-group') : null;
    var locationGrp = locationEl ? locationEl.closest('.form-group') : null;
    var targetAudienceGrp = targetAudienceEl ? targetAudienceEl.closest('.form-group') : null;
    var eventTitleGrp = eventTitleEl ? eventTitleEl.closest('.form-group') : null;
    
    var circularGrp = document.getElementById('file_circular') ? document.getElementById('file_circular').closest('.form-group') : null;
    var scheduleGrp = document.getElementById('file_schedule') ? document.getElementById('file_schedule').closest('.form-group') : null;
    var attendanceGrp = document.getElementById('file_attendance') ? document.getElementById('file_attendance').closest('.form-group') : null;
    var certificateGrp = document.getElementById('file_certificate') ? document.getElementById('file_certificate').closest('.form-group') : null;
    var resourceProfileGrp = document.getElementById('file_resource_person_profile') ? document.getElementById('file_resource_person_profile').closest('.form-group') : null;

    // Helper to change input type (e.g. text to select)
    function changeInputType(element, type, options, placeholder) {
        if (!element) return null;
        var newEl;
        if (type === 'select') {
            newEl = document.createElement('select');
            newEl.innerHTML = '<option value="">' + placeholder + '</option>';
            options.forEach(function(opt) {
                var isSelected = (element.value === opt) ? 'selected' : '';
                newEl.innerHTML += '<option value="' + opt + '" ' + isSelected + '>' + opt + '</option>';
            });
        } else {
            newEl = document.createElement('input');
            newEl.type = 'text';
            newEl.placeholder = placeholder;
            newEl.value = element.value;
        }
        newEl.id = element.id;
        newEl.name = element.name;
        newEl.className = element.className;
        var isReq = element.hasAttribute('data-required') || element.hasAttribute('required');
        if (isReq) {
            newEl.setAttribute('data-required', 'true');
        }
        if (element.parentNode) {
            element.parentNode.replaceChild(newEl, element);
        }
        return newEl;
    }

    var clubDatalist = document.createElement('datalist');
    clubDatalist.id = 'club_names_list';
    [
        'Coding Club', 'Google Developer Student Club (GDSC)', 'Robotics Club', 'STEM Club', 'Math Club', 'Project Club', 'Sector Club', 'HAM Club',
        'Film Club', 'Music Club', 'Dance Club', 'Photography Club', 'Literary Club', 'Hobby Club',
        'Women Empowerment Club', 'Civil Services Aspirants Club (CSAC)', 'Green Eco Club', 'UBA Club'
    ].forEach(function(c) {
        var opt = document.createElement('option');
        opt.value = c;
        clubDatalist.appendChild(opt);
    });
    document.body.appendChild(clubDatalist);

    function toTitleCase(str) {
        return str.replace(/\w\S*/g, function(txt){
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        }).replace(/\s+/g, ' ').trim();
    }

    function updateForm() {
        var catEl = document.getElementById('meta_activity_category');
        var activityCat = catEl ? catEl.value : '';
        
        // Re-fetch elements in case they were replaced
        eventTypeEl = document.getElementById('meta_event_type');
        topicDomainEl = document.getElementById('meta_topic_domain');
        subCatEl = document.getElementById('meta_sub_category');
        targetAudienceEl = document.getElementById('meta_target_audience');
        resourcePersonEl = document.getElementById('meta_resource_person');
        
        if (eventTypeGrp) eventTypeGrp.style.display = 'none';
        if (topicDomainGrp) topicDomainGrp.style.display = 'none';
        if (subCatGrp) subCatGrp.style.display = 'none';
        if (resourcePersonGrp) resourcePersonGrp.style.display = 'none';
        if (scheduleGrp) scheduleGrp.style.display = 'none';
        if (resourceProfileGrp) resourceProfileGrp.style.display = 'none';
        
        // Default visibility and clean state for Event Scope
        if (targetAudienceGrp && targetAudienceEl) {
            targetAudienceGrp.style.display = 'block';
            var audLabel = targetAudienceGrp.querySelector('label');
            if (audLabel) audLabel.innerText = 'Event Scope / Level';
            var existingHelp = targetAudienceGrp.querySelector('.scope-help-text');
            if (existingHelp) existingHelp.remove();

            // Re-enable all options by default
            if (targetAudienceEl.options) {
                Array.from(targetAudienceEl.options).forEach(function(opt) {
                    opt.disabled = false;
                });
            }

            // If switching away from Clubs/Stepcone to departmental streams, restore Department Wise
            if (activityCat !== 'Clubs' && activityCat !== 'Stepcone' && activityCat !== 'Professional Societies') {
                if (targetAudienceEl.value === 'Central / University Wide') {
                    targetAudienceEl.value = 'Department Wise';
                }
            }
        }
        if (eventTitleGrp) {
            eventTitleGrp.style.display = 'block';
            var etLbl = eventTitleGrp.querySelector('label');
            if (etLbl) etLbl.innerText = 'Title of the Event';
        }

        // Set Labels based on Category
        if (eventTypeGrp) {
            var el = eventTypeGrp.querySelector('label');
            if (el) el.innerText = 'Event Type';
        }
        if (subCatGrp) {
            var sl = subCatGrp.querySelector('label');
            if (sl) sl.innerText = 'Sub Category';
        }
        if (resourcePersonGrp) {
            var rl = resourcePersonGrp.querySelector('label');
            if (rl) rl.innerText = 'Resource Person Details';
        }

        var mainHeading = document.getElementById('page_main_heading');
        if (mainHeading && activityCat) {
            mainHeading.innerText = 'Upload: ' + activityCat;
        }

        var dynamicBreadcrumb = document.getElementById('dynamic-breadcrumb');
        if (dynamicBreadcrumb && activityCat) {
            dynamicBreadcrumb.innerText = activityCat;
        }

        // 1. Grad Talks & Expert Talks / Guest Lectures
        if (activityCat === 'Grad Talks' || activityCat === 'Expert Talks / Guest Lectures') {
            if (topicDomainGrp) {
                topicDomainGrp.style.display = 'block';
                if (topicDomainEl) topicDomainEl = changeInputType(topicDomainEl, 'select', ['Career Counselling', 'Awareness of Trends and Technologies', 'Domain Specific / Technical Skill', 'Other'], 'â€” Select Topic â€”');
            }
            
            if (eventTitleGrp) {
                var elLbl = eventTitleGrp.querySelector('label');
                if (elLbl) elLbl.innerText = 'Title of the Event';
            }
            
            if (resourcePersonGrp) resourcePersonGrp.style.display = 'block';
            if (resourceProfileGrp) resourceProfileGrp.style.display = 'block';
        } 
        // 2. Soft skills, Language and communication skills
        else if (activityCat === 'Soft skills' || activityCat === 'Language and communication skills') {
            if (resourcePersonGrp) {
                resourcePersonGrp.style.display = 'block';
                var rpLbl = resourcePersonGrp.querySelector('label');
                if (rpLbl) rpLbl.innerText = 'Trainer / Agency Details';
            }
            if (eventTitleGrp) {
                var etLbl = eventTitleGrp.querySelector('label');
                if (etLbl) etLbl.innerText = 'Program Name';
            }
            if (scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 3. Life skills
        else if (activityCat === 'Life skills') {
            if (subCatGrp) {
                subCatGrp.style.display = 'block';
                var scLbl = subCatGrp.querySelector('label');
                if (scLbl) scLbl.innerText = 'Specific Skill';
                if (subCatEl) subCatEl = changeInputType(subCatEl, 'select', ['Yoga', 'Physical fitness', 'Health and hygiene', 'Other'], 'â€” Select Skill â€”');
            }
            
            if (resourcePersonGrp) {
                resourcePersonGrp.style.display = 'block';
                var rpLbl = resourcePersonGrp.querySelector('label');
                if (rpLbl) rpLbl.innerText = 'Trainer / Agency Details';
            }
            if (eventTitleGrp) {
                var etLbl = eventTitleGrp.querySelector('label');
                if (etLbl) etLbl.innerText = 'Program Name';
            }
            if (scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 4. Professional Societies
        else if (activityCat === 'Professional Societies') {
            if (targetAudienceGrp) {
                var audHelp = document.createElement('small');
                audHelp.className = 'scope-help-text text-muted d-block mt-1';
                audHelp.innerHTML = '<i class="fas fa-info-circle me-1 text-primary"></i>Societies conduct both Department-Wise (branch specific) and Central (campus wide) events. Select accordingly.';
                targetAudienceGrp.appendChild(audHelp);
            }
            if (targetAudienceEl && !targetAudienceEl.value) {
                targetAudienceEl.value = 'Department Wise';
            }

            if (subCatGrp) {
                subCatGrp.style.display = 'block';
                var scLbl = subCatGrp.querySelector('label');
                if (scLbl) scLbl.innerText = 'Society Name';
                if (subCatEl) subCatEl = changeInputType(subCatEl, 'select', ['ACM', 'CSI', 'IEEE', 'IEI', 'IETE', 'IICHE', 'ISTE', 'SAE'], 'â€” Select Society â€”');
            }

            if (eventTypeGrp) {
                eventTypeGrp.style.display = 'block';
                if (eventTypeEl) eventTypeEl = changeInputType(eventTypeEl, 'select', ['Expert Talks / Guest Lectures', 'Grad Talks', 'Workshop', 'Seminar', 'Conference', 'Competition', 'Industrial Visit', 'Training Program', 'Other'], 'â€” Select Event Type â€”');
            }
            
            if (topicDomainGrp && eventTypeEl) {
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Industrial Visit' || currentEventType === 'Competition') {
                    topicDomainGrp.style.display = 'none';
                } else {
                    topicDomainGrp.style.display = 'block';
                    if (topicDomainEl) topicDomainEl = changeInputType(topicDomainEl, 'select', ['Career Counselling', 'Awareness of Trends and Technologies', 'Domain Specific / Technical Skill', 'Other'], 'â€” Select Topic / Domain â€”');
                }
            }
            
            if (resourcePersonGrp && eventTypeEl) {
                resourcePersonGrp.style.display = 'block';
                var currentEventType = eventTypeEl.value;
                var rpLbl = resourcePersonGrp.querySelector('label');
                if (rpLbl) {
                    if (currentEventType === 'Industrial Visit') {
                        rpLbl.innerText = 'Industry Name & Contact Person';
                    } else if (currentEventType === 'Competition') {
                        rpLbl.innerText = 'Jury / Evaluator Details';
                    } else {
                        rpLbl.innerText = 'Resource Person Details';
                    }
                }
            }
            
            if (scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 5. Clubs
        else if (activityCat === 'Clubs') {
            if (targetAudienceEl && targetAudienceEl.options) {
                targetAudienceEl.value = 'Central / University Wide';
                Array.from(targetAudienceEl.options).forEach(function(opt) {
                    if (opt.value === 'Department Wise') {
                        opt.disabled = true;
                    }
                });
            }
            if (targetAudienceGrp) {
                var audHelp = document.createElement('small');
                audHelp.className = 'scope-help-text text-muted d-block mt-1';
                audHelp.innerHTML = '<i class="fas fa-university me-1 text-primary"></i>Clubs are campus-wide centralized initiatives under SAC / Student Affairs.';
                targetAudienceGrp.appendChild(audHelp);
            }

            if (subCatGrp) {
                subCatGrp.style.display = 'block';
                var scLbl = subCatGrp.querySelector('label');
                if (scLbl) scLbl.innerText = 'Club Name';
                if (subCatEl) {
                    subCatEl = changeInputType(subCatEl, 'text', [], 'Enter Club Name');
                    subCatEl.setAttribute('list', 'club_names_list');
                    subCatEl.addEventListener('blur', function(e) {
                        e.target.value = toTitleCase(e.target.value);
                    });
                }
            }

            if (eventTypeGrp) {
                eventTypeGrp.style.display = 'block';
                var etLbl = eventTypeGrp.querySelector('label');
                if (etLbl) etLbl.innerText = 'Activity Type';
                if (eventTypeEl) eventTypeEl = changeInputType(eventTypeEl, 'select', ['Awareness Campaign / Drive', 'Competition / Contest', 'Exhibition / Showcase', 'Guest Lecture / Expert Talk', 'Hackathon / Ideathon', 'Workshop / Seminar', 'Other'], 'â€” Select Activity Type â€”');
            }
            
            if (topicDomainGrp) {
                topicDomainGrp.style.display = 'none';
            }
            
            if (resourcePersonGrp && eventTypeEl) {
                resourcePersonGrp.style.display = 'block';
                var currentEventType = eventTypeEl.value;
                var rpLbl = resourcePersonGrp.querySelector('label');
                if (rpLbl) {
                    if (currentEventType === 'Competition / Contest' || currentEventType === 'Hackathon / Ideathon') {
                        rpLbl.innerText = 'Jury / Evaluator Details (If any)';
                    } else if (currentEventType === 'Workshop / Seminar' || currentEventType === 'Guest Lecture / Expert Talk') {
                        rpLbl.innerText = 'Resource Person Details';
                    } else {
                        rpLbl.innerText = 'Chief Guest / Special Invitee (If any)';
                    }
                }
            }
            
            if (scheduleGrp && eventTypeEl) {
                var currentEventType = eventTypeEl.value;
                if (currentEventType === 'Competition / Contest' || currentEventType === 'Workshop / Seminar' || currentEventType === 'Hackathon / Ideathon') {
                    scheduleGrp.style.display = 'block';
                } else {
                    scheduleGrp.style.display = 'none';
                }
            }
        }
        // 6. IIC
        else if (activityCat === 'IIC') {
            if (eventTypeGrp) {
                eventTypeGrp.style.display = 'block';
                if (eventTypeEl) eventTypeEl = changeInputType(eventTypeEl, 'select', ['Workshop', 'Hackathon', 'Conference', 'Competition', 'Other'], 'â€” Select Event Type â€”');
            }
            
            if (subCatGrp) {
                subCatGrp.style.display = 'block';
                var scLbl = subCatGrp.querySelector('label');
                if (scLbl) scLbl.innerText = 'Organization / Details';
                if (subCatEl) subCatEl = changeInputType(subCatEl, 'text', [], 'Enter Details');
            }
            
            if (resourcePersonGrp) resourcePersonGrp.style.display = 'block';
            if (scheduleGrp) scheduleGrp.style.display = 'block';
        }
        // 7. Stepcone
        else if (activityCat === 'Stepcone') {
            if (targetAudienceEl && (!targetAudienceEl.value || targetAudienceEl.value === 'Department Wise')) {
                targetAudienceEl.value = 'Central / University Wide';
            }
            if (targetAudienceGrp) {
                var audHelp = document.createElement('small');
                audHelp.className = 'scope-help-text text-muted d-block mt-1';
                audHelp.innerHTML = '<i class="fas fa-globe me-1 text-primary"></i>Stepcone is a central / national level technical fest.';
                targetAudienceGrp.appendChild(audHelp);
            }

            if (eventTypeGrp) {
                eventTypeGrp.style.display = 'block';
                var etLbl = eventTypeGrp.querySelector('label');
                if (etLbl) etLbl.innerText = 'Event Type';
                if (eventTypeEl) eventTypeEl = changeInputType(eventTypeEl, 'select', ['Workshop', 'Technical Event', 'Paper Presentation', 'Project Expo', 'Hackathon / Coding Contest', 'Competition', 'Guest Lecture / Keynote', 'Other'], 'â€” Select Event Type â€”');
            }
            
            if (resourcePersonGrp) {
                resourcePersonGrp.style.display = 'block';
                var rpLbl = resourcePersonGrp.querySelector('label');
                if (rpLbl) rpLbl.innerText = 'Judge / Resource Person / Guest Details';
            }
            if (scheduleGrp) scheduleGrp.style.display = 'block';
        }

        // Toggle required attributes for all dynamic fields
        var allDynamicInputs = document.querySelectorAll('#meta_event_type, #meta_topic_domain, #meta_sub_category, #meta_resource_person, #file_schedule, #file_resource_person_profile');
        allDynamicInputs.forEach(function(el) {
            var grp = el.closest('.form-group');
            if (grp && grp.style.display === 'none') {
                el.removeAttribute('required');
            } else if (grp && grp.style.display !== 'none' && el.hasAttribute('data-required') && activityCat !== 'Stepcone') {
                el.setAttribute('required', 'required');
            }
        });

        // Stepcone: All fields (both metadata and file uploads) are non-mandatory
        if (activityCat === 'Stepcone') {
            document.querySelectorAll('#uploadForm [data-required="true"]').forEach(function(el) {
                if (el.id === 'meta_activity_category') return; // Activity category select itself remains
                el.removeAttribute('required');
            });
            document.querySelectorAll('#uploadForm .file-section input[type="file"]').forEach(function(el) {
                el.removeAttribute('required');
            });
            document.querySelectorAll('input[name="meta[event_mode]"]').forEach(function(el) {
                el.removeAttribute('required');
            });

            // Remove red asterisk from all labels in meta and file sections except activity category
            document.querySelectorAll('#uploadForm .meta-section label.required, #uploadForm .file-section label.required').forEach(function(lbl) {
                if (lbl.getAttribute('for') === 'meta_activity_category') return;
                lbl.classList.remove('required');
            });
        } else {
            // Restore required status and red asterisks for other categories
            document.querySelectorAll('#uploadForm [data-required="true"]').forEach(function(el) {
                var grp = el.closest('.form-group');
                if (!grp || grp.style.display !== 'none') {
                    el.setAttribute('required', 'required');
                    if (grp) {
                        var lbl = grp.querySelector('label');
                        if (lbl) lbl.classList.add('required');
                    }
                }
            });
            document.querySelectorAll('.file-section [data-required="true"]').forEach(function(el) {
                var grp = el.closest('.form-group');
                if (!grp || grp.style.display !== 'none') {
                    el.setAttribute('required', 'required');
                    if (grp) {
                        var lbl = grp.querySelector('label');
                        if (lbl) lbl.classList.add('required');
                    }
                }
            });
            var modeGrp = document.querySelector('input[name="meta[event_mode]"]')?.closest('.form-group');
            if (modeGrp && modeGrp.style.display !== 'none') {
                document.querySelectorAll('input[name="meta[event_mode]"]').forEach(function(r) {
                    r.setAttribute('required', 'required');
                });
                var mlbl = modeGrp.querySelector('label');
                if (mlbl) mlbl.classList.add('required');
            }
        }
    }

    function updateModeLabel() {
        var locationGrp = locationEl ? locationEl.closest('.form-group') : null;
        if (!locationGrp) return;
        var modeRadio = document.querySelector('input[name="meta[event_mode]"]:checked');
        var label = locationGrp.querySelector('label');
        if (modeRadio) {
            if (modeRadio.value === 'Online') {
                label.innerText = 'Platform Link';
                locationEl.placeholder = 'e.g., MS Teams link, Zoom link';
            } else if (modeRadio.value === 'Offline') {
                label.innerText = 'Location / Venue';
                locationEl.placeholder = 'e.g., Block 1 Seminar Hall';
            } else {
                label.innerText = 'Location / Platform Link';
                locationEl.placeholder = 'Enter Location and/or Link';
            }
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'meta_activity_category') {
            updateForm();
        } else if (e.target && e.target.id === 'meta_event_type') {
            updateForm();
        } else if (e.target && e.target.name === 'meta[event_mode]') {
            updateModeLabel();
        }
    });

    updateForm();
    updateModeLabel();
});
