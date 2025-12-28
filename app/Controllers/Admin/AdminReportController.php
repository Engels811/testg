<?php
declare(strict_types=1);

class AdminReportController
{
    public function index(): void
    {
        Security::requireTeam();

        View::render('admin/reports/index', [
            'title'   => 'Reports',
            'reports' => Report::all()
        ]);
    }

    public function show(int $id): void
    {
        Security::requireTeam();

        $report = Report::find($id);
        if (!$report) {
            Response::notFound();
        }

        View::render('admin/reports/show', [
            'title'  => 'Report #' . $id,
            'report' => $report
        ]);
    }

    public function punish(int $id): void
    {
        Security::requireTeam();
        Security::checkCsrf();

        $action   = $_POST['action'] ?? '';
        $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : null;
        $reason   = $_POST['reason'] ?? null;

        $report = Report::find($id);
        if (!$report) {
            Response::notFound();
        }

        Report::punish(
            $id,
            $report['target_username'],
            $action,
            Auth::user()->username,
            $duration,
            $reason
        );

        Response::redirect('/admin/reports/' . $id);
    }
}
