<?php
declare(strict_types=1);

/**
 * FMS Meta Table Registry
 *
 * Provides a strict server-side whitelist for resolving document_types.type_key
 * to the corresponding meta_* extension table name.
 *
 * SECURITY: Never accepts table names from user input. All resolution goes
 * through this whitelist. If a type_key is not in the registry, access is denied.
 */

/**
 * Returns the whitelisted meta table name for a given document type key.
 *
 * @param string $type_key The document type key (e.g. 'journal', 'conference')
 * @return string|null The meta table name, or null if not registered
 */
function meta_get_table(string $type_key): ?string
{
    // Strict whitelist: type_key => meta table name
    // This MUST match document_types.meta_table values in the database.
    static $registry = [
        'journal'             => 'meta_journal',
        'conference'          => 'meta_conference',
        'patent'              => 'meta_patent',
        'fdp_attended'        => 'meta_fdp_attended',
        'fdp_organised'       => 'meta_fdp_organised',
        'conf_organised'      => 'meta_conf_organised',
        'criteria_file'       => 'meta_criteria_file',
        'dept_file'           => 'meta_dept_file',
        'central_file'        => 'meta_central_file',
        'scholarship'         => 'meta_scholarship',
        'placement'           => 'meta_placement',
        'higher_ed'           => 'meta_higher_ed',
        'exam_qual'           => 'meta_exam_qual',
        'award'               => 'meta_award',
        'student_event'       => 'meta_student_event',
        'student_body'        => 'meta_student_body',
        'student_journal'     => 'meta_student_journal',
        'student_conference'  => 'meta_student_conference',
        'stu_act'             => 'document_meta_student_activity',
    ];

    return $registry[$type_key] ?? null;
}

/**
 * Returns the meta field definitions for a given document type key.
 * Each field has: name, label, type, required.
 *
 * Used by the upload form to render type-specific fields dynamically.
 *
 * @param string $type_key The document type key
 * @return array List of field definitions, empty if type unknown
 */
function meta_get_fields(string $type_key): array
{
    static $fields = [
        'journal' => [
            ['name' => 'paper_title',        'label' => 'Paper Title',         'type' => 'text',   'required' => true],
            ['name' => 'journal_name',       'label' => 'Journal Name',        'type' => 'text',   'required' => true],
            ['name' => 'authors',            'label' => 'Name of Authors',     'type' => 'author_table', 'required' => true],
            ['name' => 'issn_no',            'label' => 'ISSN No.',            'type' => 'text',   'required' => false],
            ['name' => 'volume_no',          'label' => 'Volume No.',          'type' => 'text',   'required' => false],
            ['name' => 'issue_no',           'label' => 'Issue No.',           'type' => 'text',   'required' => false],
            ['name' => 'page_no',            'label' => 'Page No.',            'type' => 'text',   'required' => false],
            ['name' => 'doi',                'label' => 'DOI',                 'type' => 'text',   'required' => false],
            ['name' => 'icr_quartile',       'label' => 'ICR Quartile',       'type' => 'select', 'options' => ['Q1', 'Q2', 'Q3', 'Q4'], 'required' => false],
            ['name' => 'scopus_quartile',    'label' => 'Scopus Quartile',    'type' => 'text',   'required' => false],
            ['name' => 'publication_link',   'label' => 'Publication Link',   'type' => 'url',    'required' => false],
            ['name' => 'indexing',           'label' => 'Indexing',           'type' => 'select', 'options' => ['SCI', 'SCIE', 'ESCI', 'SCOPUS', 'WOS', 'NON-INDEXED'], 'required' => false],
            ['name' => 'date_of_publication','label' => 'Date of Publication','type' => 'date',   'required' => false],
            ['name' => 'impact_factor',      'label' => 'Impact Factor',      'type' => 'number', 'required' => false],
            ['name' => 'quality_factor',     'label' => 'Quality Factor',     'type' => 'number', 'required' => false],
            ['name' => 'payment',            'label' => 'Payment Details',    'type' => 'select', 'options' => ['Free', 'Paid'], 'required' => false],
        ],
        'conference' => [
            ['name' => 'paper_title',        'label' => 'Paper Title',         'type' => 'text',   'required' => true],
            ['name' => 'conference_name',    'label' => 'Conference Name',     'type' => 'text',   'required' => true],
            ['name' => 'published_paper_name', 'label' => 'Name of the Paper Published', 'type' => 'text', 'required' => false],
            ['name' => 'authors',            'label' => 'Name of Authors',     'type' => 'author_table', 'required' => true],
            ['name' => 'paper_type',         'label' => 'Paper Type',          'type' => 'select', 'options' => ['Participated', 'Paper Publication', 'Poster Presentation'], 'required' => false],
            ['name' => 'volume_no',          'label' => 'Volume No.',          'type' => 'text',   'required' => false],
            ['name' => 'issue_no',           'label' => 'Issue No.',           'type' => 'text',   'required' => false],
            ['name' => 'page_no',            'label' => 'Page No.',            'type' => 'text',   'required' => false],
            ['name' => 'indexing',           'label' => 'Indexing',           'type' => 'select', 'options' => ['SCI', 'SCIE', 'ESCI', 'SCOPUS', 'WOS', 'NON-INDEXED'], 'required' => false],
            ['name' => 'publication_link',   'label' => 'Publication Link',   'type' => 'url',    'required' => false],
            ['name' => 'issn_no',            'label' => 'ISSN No.',            'type' => 'text',   'required' => false],
            ['name' => 'doi',                'label' => 'DOI',                 'type' => 'text',   'required' => false],
            ['name' => 'from_date',          'label' => 'From Date',           'type' => 'date',   'required' => false],
            ['name' => 'to_date',            'label' => 'To Date',             'type' => 'date',   'required' => false],
            ['name' => 'organised_by',       'label' => 'Organised By',        'type' => 'text',   'required' => false],
            ['name' => 'location',           'label' => 'Location',            'type' => 'text',   'required' => false],
        ],
        'patent' => [
            ['name' => 'patent_title',  'label' => 'Patent Title',    'type' => 'text',   'required' => true],
            ['name' => 'patent_no',     'label' => 'Patent No.',      'type' => 'text',   'required' => false],
            ['name' => 'patent_type',   'label' => 'Patent Type',     'type' => 'select', 'options' => ['published', 'granted'], 'required' => true],
            ['name' => 'date_of_issue', 'label' => 'Date of Issue',   'type' => 'date',   'required' => false],
            ['name' => 'inventors',     'label' => 'Inventors',       'type' => 'author_table','required' => false],
        ],
        'fdp_attended' => [
            ['name' => 'mode',         'label' => 'Mode',          'type' => 'select', 'options' => ['Online', 'Offline'], 'required' => true],
            ['name' => 'date_from',    'label' => 'From Date',     'type' => 'date', 'required' => false],
            ['name' => 'date_to',      'label' => 'To Date',       'type' => 'date', 'required' => false],
            ['name' => 'organised_by', 'label' => 'Organised By',  'type' => 'text', 'required' => false],
            ['name' => 'location',     'label' => 'Location',      'type' => 'text', 'required' => false],
        ],
        'fdp_organised' => [
            ['name' => 'mode',         'label' => 'Mode',          'type' => 'select', 'options' => ['Online', 'Offline'], 'required' => true],
            ['name' => 'date_from',    'label' => 'From Date',     'type' => 'date', 'required' => false],
            ['name' => 'date_to',      'label' => 'To Date',       'type' => 'date', 'required' => false],
            ['name' => 'organised_by', 'label' => 'Organised By',  'type' => 'text', 'required' => false],
            ['name' => 'location',     'label' => 'Location',      'type' => 'text', 'required' => false],
        ],
        'conf_organised' => [
            ['name' => 'mode',         'label' => 'Mode',          'type' => 'select', 'options' => ['Online', 'Offline'], 'required' => true],
            ['name' => 'date_from',    'label' => 'From Date',     'type' => 'date', 'required' => false],
            ['name' => 'date_to',      'label' => 'To Date',       'type' => 'date', 'required' => false],
            ['name' => 'organised_by', 'label' => 'Organised By',  'type' => 'text', 'required' => false],
            ['name' => 'location',     'label' => 'Location',      'type' => 'text', 'required' => false],
        ],
        'criteria_file' => [
            ['name' => 'criteria_no', 'label' => 'Criteria No.',   'type' => 'text',     'required' => true],
            ['name' => 'description', 'label' => 'Description',    'type' => 'textarea', 'required' => false],
            ['name' => 'semester',    'label' => 'Semester',        'type' => 'number',   'required' => false],
            ['name' => 'section',     'label' => 'Section',         'type' => 'text',     'required' => false],
            ['name' => 'ext_or_int',  'label' => 'Ext/Int',         'type' => 'text',     'required' => false],
        ],
        'dept_file' => [
            ['name' => 'file_type',     'label' => 'File Type',   'type' => 'dept_category',   'required' => true],
            ['name' => 'sub_file_type', 'label' => 'Select File Category',   'type' => 'sub_file_type',   'required' => true],
        ],
        'central_file' => [
            ['name' => 'event',      'label' => 'Event',       'type' => 'text', 'required' => false],
            ['name' => 'club_name',  'label' => 'Club Name',   'type' => 'text', 'required' => false],
            ['name' => 'event_name', 'label' => 'Event Name',  'type' => 'text', 'required' => false],
        ],
        'scholarship' => [
            ['name' => 'scheme_name',   'label' => 'Scheme Name',              'type' => 'text',   'required' => false],
            ['name' => 'gov_students',  'label' => 'Govt. Students Count',     'type' => 'number', 'required' => false],
            ['name' => 'gov_amount',    'label' => 'Govt. Amount',             'type' => 'number', 'required' => false],
            ['name' => 'inst_students', 'label' => 'Institution Students Count','type' => 'number','required' => false],
            ['name' => 'inst_amount',   'label' => 'Institution Amount',       'type' => 'number', 'required' => false],
            ['name' => 'ngo_students',  'label' => 'NGO Students Count',       'type' => 'number', 'required' => false],
            ['name' => 'ngo_amount',    'label' => 'NGO Amount',               'type' => 'number', 'required' => false],
            ['name' => 'ngo_name',      'label' => 'NGO Name',                 'type' => 'text',   'required' => false],
        ],
        'placement' => [
            ['name' => 'student_name', 'label' => 'Student Name', 'type' => 'text', 'required' => false],
            ['name' => 'programme',    'label' => 'Programme',    'type' => 'text', 'required' => false],
            ['name' => 'employer',     'label' => 'Employer',     'type' => 'text', 'required' => false],
            ['name' => 'pay',          'label' => 'Pay Package',  'type' => 'text', 'required' => false],
        ],
        'higher_ed' => [
            ['name' => 'student_name',       'label' => 'Student Name',         'type' => 'text', 'required' => false],
            ['name' => 'programme',          'label' => 'Programme',            'type' => 'text', 'required' => false],
            ['name' => 'institution',        'label' => 'Institution',          'type' => 'text', 'required' => false],
            ['name' => 'admitted_programme', 'label' => 'Admitted Programme',   'type' => 'text', 'required' => false],
        ],
        'exam_qual' => [
            ['name' => 'reg_no',      'label' => 'Reg. No.',       'type' => 'text', 'required' => false],
            ['name' => 'exam',        'label' => 'Exam Name',      'type' => 'text', 'required' => false],
            ['name' => 'exam_status', 'label' => 'Exam Status',    'type' => 'radio', 'options' => ['Pass', 'Fail'], 'required' => false],
            ['name' => 'rank',        'label' => 'Rank',           'type' => 'number', 'required' => false],
        ],
        'award' => [
            ['name' => 'award_name',          'label' => 'Award Name',           'type' => 'text', 'required' => false],
            ['name' => 'participation_type',  'label' => 'Participation Type',   'type' => 'text', 'required' => false],
            ['name' => 'student_name',        'label' => 'Student Name',         'type' => 'text', 'required' => false],
            ['name' => 'competition_level',   'label' => 'Competition Level',    'type' => 'text', 'required' => false],
            ['name' => 'event_name',          'label' => 'Event Name',           'type' => 'text', 'required' => false],
            ['name' => 'month_year',          'label' => 'Month/Year',           'type' => 'text', 'required' => false],
        ],
        'student_event' => [
            ['name' => 'activity',              'label' => 'Activity',             'type' => 'text', 'required' => false],
            ['name' => 'event_name',            'label' => 'Event Name',           'type' => 'text', 'required' => false],
            ['name' => 'from_date',             'label' => 'From Date',            'type' => 'date', 'required' => false],
            ['name' => 'to_date',               'label' => 'To Date',              'type' => 'date', 'required' => false],
            ['name' => 'organised_by',          'label' => 'Organised By',         'type' => 'text', 'required' => false],
            ['name' => 'location',              'label' => 'Location',             'type' => 'text', 'required' => false],
            ['name' => 'participation_status',  'label' => 'Participation Status', 'type' => 'select', 'options' => ['Participated', '1st', '2nd', '3rd'], 'required' => false],
        ],
        'student_body' => [
            ['name' => 'body_name',             'label' => 'Professional Body',    'type' => 'select', 'options' => ['ISTE', 'CSI', 'ACM', 'ACMW', 'Coding Club', 'IEEE', 'IEEE-WIE'], 'required' => false],
            ['name' => 'event_name',            'label' => 'Event Name',           'type' => 'text', 'required' => false],
            ['name' => 'from_date',             'label' => 'From Date',            'type' => 'date', 'required' => false],
            ['name' => 'to_date',               'label' => 'To Date',              'type' => 'date', 'required' => false],
            ['name' => 'organised_by',          'label' => 'Organised By',         'type' => 'text', 'required' => false],
            ['name' => 'location',              'label' => 'Location',             'type' => 'text', 'required' => false],
            ['name' => 'participation_status',  'label' => 'Participation Status', 'type' => 'select', 'options' => ['Participated', '1st', '2nd', '3rd'], 'required' => false],
        ],
        'student_journal' => [
            ['name' => 'paper_title',         'label' => 'Paper Title',          'type' => 'text',   'required' => false],
            ['name' => 'journal_name',        'label' => 'Journal Name',         'type' => 'text',   'required' => false],
            ['name' => 'indexing',            'label' => 'Indexing',            'type' => 'text',   'required' => false],
            ['name' => 'date_of_submission',  'label' => 'Date of Submission',  'type' => 'date',   'required' => false],
            ['name' => 'impact_factor',       'label' => 'Impact Factor',       'type' => 'number', 'required' => false],
            ['name' => 'quality_factor',      'label' => 'Quality Factor',      'type' => 'number', 'required' => false],
            ['name' => 'payment',             'label' => 'Payment Details',     'type' => 'text',   'required' => false],
        ],
        'student_conference' => [
            ['name' => 'paper_title',  'label' => 'Paper Title',   'type' => 'text', 'required' => false],
            ['name' => 'paper_type',   'label' => 'Paper Type',    'type' => 'text', 'required' => false],
            ['name' => 'from_date',    'label' => 'From Date',     'type' => 'date', 'required' => false],
            ['name' => 'to_date',      'label' => 'To Date',       'type' => 'date', 'required' => false],
            ['name' => 'organised_by', 'label' => 'Organised By',  'type' => 'text', 'required' => false],
            ['name' => 'location',     'label' => 'Location',      'type' => 'text', 'required' => false],
        ],
        'stu_act' => [
            ['name' => 'activity_category', 'label' => 'Activity Category', 'type' => 'text', 'required' => true],
            ['name' => 'participation_type', 'label' => 'Participation Type', 'type' => 'text', 'required' => true],
            ['name' => 'event_details', 'label' => 'Event Details', 'type' => 'json', 'required' => false],
            ['name' => 'participants', 'label' => 'Participants', 'type' => 'json', 'required' => false],
        ],
    ];

    return $fields[$type_key] ?? [];
}

/**
 * Returns the file slot definitions for a given document type key.
 * Each slot has: name, label, required, accept (mime types).
 *
 * Most types have a single file slot. Types like fdp_organised have multiple
 * file slots (brochure, schedule, attendance, etc.).
 *
 * @param string $type_key The document type key
 * @return array List of file slot definitions
 */
function meta_get_file_slots(string $type_key): array
{
    static $slots = [
        'journal'     => [['name' => 'paper_file',   'label' => 'Paper File',   'required' => true, 'accept' => '.pdf,.doc,.docx']],
        'conference'  => [['name' => 'certificate',   'label' => 'Certificate',  'required' => true, 'accept' => '.pdf,.jpg,.png'],
                          ['name' => 'paper_file',    'label' => 'Paper File',   'required' => false,'accept' => '.pdf,.doc,.docx']],
        'patent'      => [['name' => 'patent_file',   'label' => 'Patent File',  'required' => true, 'accept' => '.pdf']],
        'fdp_attended'=> [['name' => 'certificate',   'label' => 'Certificate',  'required' => true, 'accept' => '.pdf,.jpg,.png'],
                          ['name' => 'brochure',      'label' => 'Brochure',     'required' => false,'accept' => '.pdf,.jpg,.png']],
        'fdp_organised' => [
            ['name' => 'certificate',           'label' => 'Certificate',           'required' => false, 'accept' => '.pdf,.jpg,.png'],
            ['name' => 'brochure',              'label' => 'Brochure',              'required' => true,  'accept' => '.pdf,.jpg,.png'],
            ['name' => 'fdp_schedule_invitation','label' => 'Schedule/Invitation',  'required' => true,  'accept' => '.pdf,.jpg,.png'],
            ['name' => 'attendance_forms',      'label' => 'Attendance Forms',      'required' => true,  'accept' => '.pdf,.jpg,.png'],
            ['name' => 'feedback_forms',        'label' => 'Feedback Forms',        'required' => true,  'accept' => '.pdf,.jpg,.png'],
            ['name' => 'fdp_report',            'label' => 'FDP Report',            'required' => true,  'accept' => '.pdf,.doc,.docx'],
            ['name' => 'photo1',                'label' => 'Photo 1',               'required' => true,  'accept' => '.jpg,.png,.jpeg'],
            ['name' => 'photo2',                'label' => 'Photo 2',               'required' => true,  'accept' => '.jpg,.png,.jpeg'],
            ['name' => 'photo3',                'label' => 'Photo 3',               'required' => true,  'accept' => '.jpg,.png,.jpeg'],
        ],
        'conf_organised' => [
            ['name' => 'brochure',              'label' => 'Brochure',              'required' => false, 'accept' => '.pdf,.jpg,.png'],
            ['name' => 'fdp_schedule_invitation','label' => 'Schedule/Invitation',  'required' => false, 'accept' => '.pdf,.jpg,.png'],
            ['name' => 'attendance_forms',      'label' => 'Attendance Forms',      'required' => false, 'accept' => '.pdf,.jpg,.png'],
            ['name' => 'feedback_forms',        'label' => 'Feedback Forms',        'required' => false, 'accept' => '.pdf,.jpg,.png'],
            ['name' => 'fdp_report',            'label' => 'Conference Report',     'required' => false, 'accept' => '.pdf,.doc,.docx'],
            ['name' => 'photo1',                'label' => 'Photo 1',               'required' => false, 'accept' => '.jpg,.png,.jpeg'],
            ['name' => 'photo2',                'label' => 'Photo 2',               'required' => false, 'accept' => '.jpg,.png,.jpeg'],
            ['name' => 'photo3',                'label' => 'Photo 3',               'required' => false, 'accept' => '.jpg,.png,.jpeg'],
        ],
        'criteria_file'  => [['name' => 'document_file', 'label' => 'Document File', 'required' => true, 'accept' => '.pdf,.doc,.docx,.xls,.xlsx']],
        'dept_file'      => [['name' => 'document_file', 'label' => 'Document File', 'required' => true, 'accept' => '.pdf,.doc,.docx,.xls,.xlsx']],
        'central_file'   => [['name' => 'document_file', 'label' => 'Document File', 'required' => true, 'accept' => '.pdf,.doc,.docx,.xls,.xlsx'],
                              ['name' => 'photo1', 'label' => 'Photo 1', 'required' => false, 'accept' => '.jpg,.png,.jpeg'],
                              ['name' => 'photo2', 'label' => 'Photo 2', 'required' => false, 'accept' => '.jpg,.png,.jpeg'],
                              ['name' => 'photo3', 'label' => 'Photo 3', 'required' => false, 'accept' => '.jpg,.png,.jpeg'],
                              ['name' => 'photo4', 'label' => 'Photo 4', 'required' => false, 'accept' => '.jpg,.png,.jpeg']],
        'scholarship'    => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.xls,.xlsx']],
        'placement'      => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.xls,.xlsx']],
        'higher_ed'      => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.xls,.xlsx']],
        'exam_qual'      => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.xls,.xlsx']],
        'award'          => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.xls,.xlsx,.jpg,.png']],
        'student_event'  => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.jpg,.png']],
        'student_body'   => [['name' => 'document_file', 'label' => 'Supporting Document', 'required' => true, 'accept' => '.pdf,.jpg,.png']],
        'student_journal'=> [['name' => 'paper_file',    'label' => 'Paper File',           'required' => true, 'accept' => '.pdf,.doc,.docx']],
        'student_conference' => [['name' => 'certificate', 'label' => 'Certificate',        'required' => true, 'accept' => '.pdf,.jpg,.png'],
                                  ['name' => 'paper_file', 'label' => 'Paper File',          'required' => false,'accept' => '.pdf,.doc,.docx']],
        'stu_act'        => [['name' => 'proof_file',    'label' => 'Proof File',           'required' => true, 'accept' => '.pdf']],
    ];

    return $slots[$type_key] ?? [['name' => 'document_file', 'label' => 'Document File', 'required' => true, 'accept' => '.pdf,.doc,.docx']];
}

/**
 * Validates that a type_key is registered and returns its meta table name.
 * Throws an exception if the type_key is not whitelisted.
 *
 * @param string $type_key
 * @return string The validated meta table name
 * @throws InvalidArgumentException if type_key is not registered
 */
function meta_require_table(string $type_key): string
{
    $table = meta_get_table($type_key);
    if ($table === null) {
        throw new InvalidArgumentException("Unknown document type key: " . htmlspecialchars($type_key));
    }
    return $table;
}
