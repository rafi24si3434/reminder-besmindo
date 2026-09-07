<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model
{
    protected $table = 'attendances';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_meeting_attendances($meeting_id)
    {
        $this->db->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, crews.phone, crews.group_code, crews.rig_id, rigs.name as rig_name, rigs.code as rig_code');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->where('attendances.meeting_id', $meeting_id);
        $this->db->order_by('crews.rig_id', 'ASC');
        $this->db->order_by('crews.group_code', 'ASC');
        $this->db->order_by('crews.name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function init_meeting_attendances($meeting_id)
    {
        $participants = $this->db->get_where('meeting_participants', array('meeting_id' => $meeting_id))->result_array();

        foreach ($participants as $p) {
            $exist = $this->db->get_where($this->table, array('meeting_id' => $meeting_id, 'crew_id' => $p['crew_id']))->row_array();
            if (!$exist) {
                $this->db->insert($this->table, array(
                    'meeting_id' => $meeting_id,
                    'crew_id'    => $p['crew_id'],
                    'status'     => 'BELUM_HADIR',
                    'source'     => 'TEAMS_SIMULATOR'
                ));
            }
        }
    }

    public function get_attendance_stats($meeting_id = NULL)
    {
        if ($meeting_id) {
            $this->db->where('meeting_id', $meeting_id);
        }
        $all = $this->db->get($this->table)->result_array();
        $total = count($all);
        $hadir = 0;
        $belum_hadir = 0;
        $tidak_hadir = 0;
        $izin = 0;

        foreach ($all as $row) {
            if ($row['status'] === 'HADIR') $hadir++;
            elseif ($row['status'] === 'BELUM_HADIR') $belum_hadir++;
            elseif ($row['status'] === 'TIDAK_HADIR') $tidak_hadir++;
            elseif ($row['status'] === 'IZIN') $izin++;
        }

        $present_count = $hadir;
        $percentage = $total > 0 ? round(($present_count / $total) * 100, 1) : 0;

        return array(
            'total'       => $total,
            'hadir'       => $hadir,
            'belum_hadir' => $belum_hadir,
            'tidak_hadir' => $tidak_hadir,
            'izin'        => $izin,
            'present'     => $present_count,
            'percentage'  => $percentage
        );
    }

    public function get_most_absent_crews($limit = 10)
    {
        $this->db->select('crews.id, crews.nik, crews.name, crews.position, crews.group_code, rigs.name as rig_name, 
                           COUNT(attendances.id) as total_invited,
                           SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as count_hadir,
                           SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as count_tidak_hadir,
                           SUM(CASE WHEN attendances.status = "BELUM_HADIR" THEN 1 ELSE 0 END) as count_belum_hadir,
                           SUM(CASE WHEN attendances.status = "IZIN" THEN 1 ELSE 0 END) as count_izin');
        $this->db->from('crews');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->join('attendances', 'attendances.crew_id = crews.id', 'left');
        $this->db->where('crews.is_active', 1);
        $this->db->group_by('crews.id');
        $this->db->order_by('count_tidak_hadir', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    // Alias for backward compatibility
    public function get_most_absent_or_late_crew($limit = 10)
    {
        return $this->get_most_absent_crews($limit);
    }

    public function get_recap_by_rig()
    {
        $this->db->select('rigs.id, rigs.name, rigs.code, rigs.pj_name,
                           COUNT(attendances.id) as total_meeting_records,
                           SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as count_hadir,
                           SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as count_tidak_hadir,
                           SUM(CASE WHEN attendances.status = "IZIN" THEN 1 ELSE 0 END) as count_izin');
        $this->db->from('rigs');
        $this->db->join('crews', 'crews.rig_id = rigs.id', 'left');
        $this->db->join('attendances', 'attendances.crew_id = crews.id', 'left');
        $this->db->where('rigs.is_active', 1);
        $this->db->group_by('rigs.id');
        return $this->db->get()->result_array();
    }

    /**
     * Get 1-Year Attendance Statistics for Donut & Bar Charts
     */
    public function get_yearly_attendance_stats($year = NULL, $crew_id = NULL, $rig_id = NULL)
    {
        if (empty($year)) {
            $year = date('Y');
        }

        // 1. Monthly data for 12 months (Jan - Dec)
        $months = array(
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        );

        $this->db->select('MONTH(meetings.meeting_date) as month_num,
                           SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as count_hadir,
                           SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as count_tidak_hadir,
                           SUM(CASE WHEN attendances.status = "IZIN" THEN 1 ELSE 0 END) as count_izin');
        $this->db->from('attendances');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->where('YEAR(meetings.meeting_date)', (int)$year);

        if (!empty($crew_id)) {
            $this->db->where('attendances.crew_id', (int)$crew_id);
        }
        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', (int)$rig_id);
        }

        $this->db->group_by('month_num');
        $dbMonthly = $this->db->get()->result_array();

        $monthlyData = array();
        $monthlyIndex = array();
        foreach ($dbMonthly as $row) {
            $monthlyIndex[(int)$row['month_num']] = $row;
        }

        $totalHadir = 0;
        $totalTidakHadir = 0;
        $totalIzin = 0;

        foreach ($months as $num => $label) {
            $hadir = isset($monthlyIndex[$num]) ? (int)$monthlyIndex[$num]['count_hadir'] : 0;
            $tidak = isset($monthlyIndex[$num]) ? (int)$monthlyIndex[$num]['count_tidak_hadir'] : 0;
            $izin  = isset($monthlyIndex[$num]) ? (int)$monthlyIndex[$num]['count_izin'] : 0;

            $monthlyData[] = array(
                'month'       => $num,
                'label'       => $label,
                'hadir'       => $hadir,
                'tidak_hadir' => $tidak,
                'izin'        => $izin,
                'total'       => ($hadir + $tidak + $izin)
            );

            $totalHadir += $hadir;
            $totalTidakHadir += $tidak;
            $totalIzin += $izin;
        }

        // 2. Summary for Donut Chart
        $overallTotal = $totalHadir + $totalTidakHadir + $totalIzin;
        $attendanceRate = $overallTotal > 0 ? round(($totalHadir / $overallTotal) * 100, 1) : 0;

        // 3. Crew Performance breakdown in selected year
        $this->db->select('crews.id, crews.name, crews.phone, crews.position, crews.group_code, rigs.name as rig_name,
                           COUNT(attendances.id) as total_meetings,
                           SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as hadir,
                           SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as tidak_hadir,
                           SUM(CASE WHEN attendances.status = "IZIN" THEN 1 ELSE 0 END) as izin');
        $this->db->from('crews');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->join('attendances', 'attendances.crew_id = crews.id', 'left');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id AND YEAR(meetings.meeting_date) = ' . (int)$year, 'left');
        $this->db->where('crews.is_active', 1);

        if (!empty($crew_id)) {
            $this->db->where('crews.id', (int)$crew_id);
        }
        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', (int)$rig_id);
        }

        $this->db->group_by('crews.id');
        $this->db->order_by('crews.group_code', 'ASC');
        $this->db->order_by('crews.name', 'ASC');
        $crewSummary = $this->db->get()->result_array();

        foreach ($crewSummary as &$c) {
            $tot = (int)$c['total_meetings'];
            $had = (int)$c['hadir'];
            $c['percentage'] = $tot > 0 ? round(($had / $tot) * 100, 1) : 0;
        }

        return array(
            'year'           => (int)$year,
            'monthly'        => $monthlyData,
            'donut'          => array(
                'hadir'       => $totalHadir,
                'tidak_hadir' => $totalTidakHadir,
                'izin'        => $totalIzin,
                'total'       => $overallTotal,
                'percentage'  => $attendanceRate
            ),
            'crew_summary'   => $crewSummary
        );
    }

    public function close_meeting_session($meeting_id, $meeting_notes = null)
    {
        // 1. Ubah yang masih BELUM_HADIR menjadi TIDAK_HADIR (Alpha)
        $this->db->where('meeting_id', $meeting_id);
        $this->db->where('status', 'BELUM_HADIR');
        $this->db->update($this->table, array(
            'status' => 'TIDAK_HADIR',
            'notes'  => 'Tidak hadir hingga sesi rapat ditutup'
        ));

        // 2. Tandai status meeting menjadi completed dan simpan waktu tutup + notulensi
        $this->db->where('id', $meeting_id);
        return $this->db->update('meetings', array(
            'status'        => 'completed',
            'closed_at'     => date('Y-m-d H:i:s'),
            'meeting_notes' => $meeting_notes
        ));
    }

    public function reopen_meeting_session($meeting_id)
    {
        $this->db->where('id', $meeting_id);
        return $this->db->update('meetings', array(
            'status'    => 'in_progress',
            'closed_at' => NULL
        ));
    }

    public function update_meeting_notes($meeting_id, $meeting_notes)
    {
        $this->db->where('id', $meeting_id);
        return $this->db->update('meetings', array(
            'meeting_notes' => $meeting_notes
        ));
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
}
