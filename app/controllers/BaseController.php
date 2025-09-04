<?php
// Base Controller Class
class BaseController {
    protected $data = [];
    
    protected function view($viewName, $data = []) {
        // Merge data
        $this->data = array_merge($this->data, $data);
        
        // Extract data to variables
        extract($this->data);
        
        // Include the view file
        $viewFile = APP_PATH . "/views/{$viewName}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View not found: {$viewName}";
        }
    }
    
    protected function redirect($url) {
        Config::redirect($url);
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    protected function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Config::validateCSRFToken($token)) {
                $this->redirect('/');
            }
        }
    }
    
    protected function sanitizeInput($input) {
        return Config::sanitizeInput($input);
    }
    
    protected function uploadFile($file, $uploadDir = 'public/uploads/') {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return false;
            default:
                return false;
        }
        
        if ($file['size'] > Config::MAX_FILE_SIZE) {
            return false;
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowedTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $ext = array_search($mimeType, $allowedTypes, true);
        if ($ext === false) {
            return false;
        }
        
        $fileName = sprintf('%s.%s', uniqid(), $ext);
        $uploadPath = ROOT_PATH . '/' . $uploadDir . $fileName;
        
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'file_name' => $fileName,
                'file_path' => $uploadDir . $fileName,
                'file_type' => $mimeType,
                'file_size' => $file['size']
            ];
        }
        
        return false;
    }
}
?>
