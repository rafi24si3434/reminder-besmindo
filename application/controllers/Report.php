<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model');
        $this->load->model('Crew_model');
        $this->load->model('Rig_model');
        $this->load->model('Meeting_model');
    }

    public function index()
    {
        $rig_id = $this->input->get('rig_id', TRUE);
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $rigs = $this->Rig_model->get_all();
        $stats = $this->Attendance_model->get_attendance_stats();
        $most_absent_crews = $this->Attendance_model->get_most_absent_or_late_crew(10);
        $rig_recaps = $this->Attendance_model->get_recap_by_rig();

        $this->db->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, rigs.code as rig_code, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', (int)$rig_id);
        }
        if (!empty($start_date)) {
            $this->db->where('meetings.meeting_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('meetings.meeting_date <=', $end_date);
        }

        $records = $this->db->order_by('meetings.meeting_date', 'DESC')->order_by('crews.name', 'ASC')->get()->result_array();

        $data = array(
            'title'           => 'Rekapitulasi Kehadiran Meeting - Besmindo Reminder',
            'rigs'            => $rigs,
            'stats'           => $stats,
            'mostAbsentCrews' => $most_absent_crews,
            'rigRecaps'       => $rig_recaps,
            'records'         => $records,
            'selectedRig'     => $rig_id,
            'startDate'       => $start_date,
            'endDate'         => $end_date
        );

        $this->render_template('report/index', $data);
    }

    public function print_report()
    {
        $rig_id = $this->input->get('rig_id', TRUE);
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $this->db->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, rigs.code as rig_code, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', (int)$rig_id);
        }
        if (!empty($start_date)) {
            $this->db->where('meetings.meeting_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('meetings.meeting_date <=', $end_date);
        }

        $records = $this->db->order_by('meetings.meeting_date', 'DESC')->get()->result_array();
        $stats = $this->Attendance_model->get_attendance_stats();

        $this->load->view('report/print', array(
            'title'     => 'Laporan Kehadiran Meeting Crew Rig - PT Besmindo',
            'records'   => $records,
            'stats'     => $stats,
            'startDate' => $start_date,
            'endDate'   => $end_date
        ));
    }

    public function export_excel()
    {
        $rig_id = $this->input->get('rig_id', TRUE);
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $this->db->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', (int)$rig_id);
        }
        if (!empty($start_date)) {
            $this->db->where('meetings.meeting_date >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('meetings.meeting_date <=', $end_date);
        }

        $records = $this->db->order_by('meetings.meeting_date', 'DESC')->get()->result_array();

        $filename = 'Rekap_Kehadiran_Crew_Rig_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

        fputcsv($output, array('No', 'Tanggal', 'Jam', 'Nama Meeting', 'Unit Rig', 'NIK', 'Nama Crew', 'Jabatan', 'Status Kehadiran', 'Waktu Join', 'Durasi (Menit)', 'Sumber Data', 'Catatan'));

        $no = 1;
        foreach ($records as $r) {
            fputcsv($output, array(
                $no++,
                $r['meeting_date'],
                substr($r['start_time'], 0, 5) . ' WIB',
                $r['meeting_title'],
                $r['rig_name'],
                $r['nik'],
                $r['crew_name'],
                $r['position'],
                $r['status'],
                $r['join_time'] ?: '-',
                $r['duration_minutes'] ?: '0',
                $r['source'],
                $r['notes'] ?: '-'
            ));
        }

        fclose($output);
        exit;
    }
}
