<?php

class Home extends Controller
{
public function index()
{
    $provModel = $this->model('Provinces');
    
    $data['provinces'] = $provModel->getAllProvincesData();
    $data['nasional'] = $provModel->getNationalStats();
    $data['disasters'] = $provModel->getLatestDisasters();
    $data['trends'] = $provModel->getNationalTrends(); 
    $data['top_issues'] = $provModel->getTopIssues();
    
    $data['title'] = 'Dashboard Kerawanan Nasional';

    $this->view('templates/header', $data);
    $this->view('home/index', $data);
    $this->view('templates/footer', $data);
}

    public function api()
    {
        // Simple API for AJAX requests if needed later
        header('Content-Type: application/json');
        $provModel = $this->model('Provinces');
        echo json_encode($provModel->getAllProvincesData());
    }
}
