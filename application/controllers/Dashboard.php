<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Crew_model');
        $this->load->model('Rig_model');
        $this->load->model('Attendance_model');
        $this->load->model('Reminder_model');
    }

    public function index()
    {
        $today_meetings = $this->Meeting_model->get_today_meetings();
        $upcoming_meetings = $this->Meeting_model->get_upcoming_meetings(6);

        $total_crews = count($this->Crew_model->get_crews_with_rig(null, 1));
        $total_rigs = count($this->Rig_model->get_all(true));
        $total_meetings = count($this->Meeting_model->get_meetings_detailed());

        $attendance_stats = $this->Attendance_model->get_attendance_stats();
        $rig_summaries = $this->Rig_model->get_rig_with_crew_count();
        $recent_reminders = $this->Reminder_model->get_logs(null, 5);

        $data = array(
            'title'             => 'Dashboard Manager - Besmindo Reminder',
            'today_meetings'    => $today_meetings,
            'upcoming_meetings' => $upcoming_meetings,
            'total_crews'       => $total_crews,
            'total_rigs'        => $total_rigs,
            'total_meetings'    => $total_meetings,
            'attendance_stats'  => $attendance_stats,
            'rig_summaries'     => $rig_summaries,
            'recent_reminders'  => $recent_reminders
        );

        $this->render_template('dashboard/index', $data);
    }
}
