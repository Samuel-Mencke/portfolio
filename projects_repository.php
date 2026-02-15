<?php
// Neue zentrale DB-Konfiguration laden
require_once __DIR__ . '/config/db_config.php';

class ProjectsRepository {
    private $pdo;

    public function __construct() {
        global $config;
        $this->pdo = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8',
            $config['user'],
            $config['password']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllProjects() {
        $stmt = $this->pdo->query('SELECT id, title, description, github, website, technology, image, slug FROM projects ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectBySlug($slug) {
        $sql = "SELECT id, title, description, github, website, technology, image, slug FROM projects WHERE slug = :slug LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $project = $stmt->fetch();
        return $project === false ? null : $project;
    }
}
