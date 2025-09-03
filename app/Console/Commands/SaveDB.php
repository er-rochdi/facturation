<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;

class SaveDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-db {action : backup|restore} {filename? : Nom du fichier de backup à restaurer (avec ou sans extension .zip)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sauvegarde et restauration de base de données avec compression ZIP';

    private $backupPath;

    public function __construct()
    {
        parent::__construct();
        $this->backupPath = base_path('database/backups');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if (!in_array($action, ['backup', 'restore'])) {
            $this->error('Action invalide. Utilisez "backup" ou "restore".');
            return 1;
        }

        return $action === 'backup' ? $this->backup() : $this->restore();
    }

    private function backup()
    {
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $sqlFilename = $timestamp . '.sql';
        $zipFilename = $timestamp . '.zip';

        // Créer le dossier s'il n'existe pas
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }

        $sqlPath = $this->backupPath . '/' . $sqlFilename;
        $zipPath = $this->backupPath . '/' . $zipFilename;

        $this->info('Création du dump de la base de données...');

        // Créer le dump SQL
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $sqlPath
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Erreur lors de la création du dump SQL');
            return 1;
        }

        $this->info('Compression du fichier en ZIP...');

        // Créer l'archive ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            $this->error('Impossible de créer l\'archive ZIP');
            // Supprimer le fichier SQL temporaire
            if (File::exists($sqlPath)) {
                File::delete($sqlPath);
            }
            return 1;
        }

        // Ajouter le fichier SQL à l'archive
        $zip->addFile($sqlPath, $sqlFilename);
        $zip->close();

        // Supprimer le fichier SQL temporaire
        if (File::exists($sqlPath)) {
            File::delete($sqlPath);
        }

        // Vérifier que l'archive a été créée
        if (File::exists($zipPath)) {
            $fileSize = $this->formatBytes(File::size($zipPath));
            $this->info("Base de données sauvegardée avec succès dans database/backups/{$zipFilename}");
            $this->info("Taille du fichier compressé : {$fileSize}");
            return 0;
        }

        $this->error('Erreur lors de la création de l\'archive ZIP');
        return 1;
    }

    private function restore()
    {
        $filename = $this->argument('filename');
        $zipPath = null;

        if ($filename) {
            // Ajouter l'extension .zip si elle n'est pas présente
            if (!str_ends_with($filename, '.zip')) {
                $filename .= '.zip';
            }

            $zipPath = $this->backupPath . '/' . $filename;
            if (!File::exists($zipPath)) {
                $this->error("Le fichier {$filename} n'existe pas dans database/backups/");
                return 1;
            }
        } else {
            // Récupérer le dernier fichier ZIP de backup
            if (!File::exists($this->backupPath)) {
                $this->error('Le dossier database/backups/ n\'existe pas');
                return 1;
            }

            $files = File::glob($this->backupPath . '/*.zip');
            if (empty($files)) {
                $this->error('Aucun fichier de backup ZIP trouvé');
                return 1;
            }

            // Trier les fichiers par date de modification (le plus récent en premier)
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            $zipPath = $files[0];
            $filename = basename($zipPath);
        }

        $this->info("Décompression de {$filename}...");

        // Extraire l'archive ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            $this->error('Impossible d\'ouvrir l\'archive ZIP');
            return 1;
        }

        // Créer un dossier temporaire pour l'extraction
        $tempDir = $this->backupPath . '/temp_' . uniqid();
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Extraire l'archive
        $zip->extractTo($tempDir);
        $zip->close();

        // Trouver le fichier SQL dans le dossier temporaire
        $sqlFiles = File::glob($tempDir . '/*.sql');
        if (empty($sqlFiles)) {
            $this->error('Aucun fichier SQL trouvé dans l\'archive');
            // Nettoyer le dossier temporaire
            File::deleteDirectory($tempDir);
            return 1;
        }

        $sqlPath = $sqlFiles[0];

        $this->info('Restauration de la base de données...');

        // Restaurer la base de données
        $command = sprintf(
            'mysql -u%s -p%s %s < %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $sqlPath
        );

        exec($command, $output, $returnVar);

        // Nettoyer le dossier temporaire
        File::deleteDirectory($tempDir);

        if ($returnVar === 0) {
            $this->info("Base de données restaurée avec succès depuis {$filename}");
            return 0;
        }

        $this->error('Erreur lors de la restauration de la base de données');
        return 1;
    }

    /**
     * Formate la taille d'un fichier en octets vers une unité lisible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
