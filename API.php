<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once('MysqliDb.php');

class API {
    public function __construct()
    {
        $this->db = new MysqliDb('localhost', 'root', '9plus28maratas', 'employee');
    }

    /**
     * HTTP GET Request
     *
     * @param $payload
     */
    public function httpGet($payload)
{
    if (!is_array($payload)) {
        return [
            
            'status' => 'error',
            'message' => 'Invalid Payload. Payload must be an array.'
        ];
    }

    try {
        foreach ($payload as $column => $value) {
            $this->db->where($column, $value);
        }

        $results = $this->db->get('tbl_to_do_list');
        if ($results) {
            return [
                
                'status' => 'success',
                'data' => $results
            ];
        } else {
            return [
                
                'status' => 'fail',
                'message' => 'Failed Fetch Request'
            ];
        }
    } catch (Exception $e) {
        file_put_contents('error_log.txt', "GET Error: " . $e->getMessage(), FILE_APPEND);
        return [
            
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ];
    }
}


    /**
     * HTTP POST Request
     *
     * @param $payload
     */
    public function httpPost($payload)
    {
        if (!is_array($payload) || empty($payload)) {
            return [
                
                'status' => 'error',
                'message' => 'Invalid or empty payload.'
            ];
        }
    
        try {
            $id = $this->db->insert('tbl_to_do_list', $payload);
            if ($id) {
                return [
                    
                    'status' => 'success',
                    'message' => 'Data successfully inserted',
                    'id' => $id
                ];
            } else {
                return [
                    
                    'status' => 'fail',
                    'message' => 'Failed to insert data'
                ];
            }
        } catch (Exception $e) {
            file_put_contents('error_log.txt', "POST Error: " . $e->getMessage(), FILE_APPEND);
            return [
                
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    

    /**
     * HTTP PUT Request
     *
     * @param $id
     * @param $payload
     */
    public function httpPut($id, $payload)
{
    if (empty($id)) {
        return [
            
            'status' => 'error',
            'message' => 'ID cannot be empty.'
        ];
    }

    if (empty($payload)) {
        return [
            
            'status' => 'error',
            'message' => 'Payload cannot be empty.'
        ];
    }

    if ($id != $payload['id']) {
        return [
            
            'status' => 'error',
            'message' => 'ID mismatch.'
        ];
    }

    try {
        $this->db->where('id', $id);
        if ($this->db->update('tbl_to_do_list', $payload)) {
            return [
                
                'status' => 'success',
                'message' => 'Data successfully updated'
            ];
        } else {
            return [
                
                'status' => 'fail',
                'message' => 'Failed to update data'
            ];
        }
    } catch (Exception $e) {
        file_put_contents('error_log.txt', "PUT Error: " . $e->getMessage(), FILE_APPEND);
        return [
            
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ];
    }
}


    /**
     * HTTP DELETE Request
     *
     * @param $id
     * @param $payload
     */
    public function httpDelete($id, $payload)
    {
        if (empty($id)) {
            return [
                
                'status' => 'error',
                'message' => 'ID cannot be empty.'
            ];
        }
    
        if (!is_array($payload) || empty($payload)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('id', $payload, 'IN');
        }
    
        try {
            if ($this->db->delete('tbl_to_do_list')) {
                return [
                    
                    'status' => 'success',
                    'message' => 'Data successfully deleted'
                ];
            } else {
                return [
                    
                    'status' => 'fail',
                    'message' => 'Failed to delete data'
                ];
            }
        } catch (Exception $e) {
            file_put_contents('error_log.txt', "DELETE Error: " . $e->getMessage(), FILE_APPEND);
            return [
                
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
}

// Handling the request
$request_method = $_SERVER['REQUEST_METHOD'];
$received_data = [];
$ids = null; 

if ($request_method === 'GET') {
    $received_data = $_GET;
} else {
    if ($request_method === 'PUT' || $request_method === 'DELETE') {
        $request_uri = $_SERVER['REQUEST_URI'];
        $exploded_request_uri = array_values(explode("/", $request_uri));
        $ids = end($exploded_request_uri); 
    }
    $raw_input = file_get_contents('php://input');
    $received_data = json_decode($raw_input, true);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'method' => 'PUT',
            'status' => 'error',
            'message' => 'Invalid JSON: ' . json_last_error_msg()
        ];
    }

    // Check if the payload is empty
    if (empty($received_data)) {
        return [
            'method' => 'PUT',
            'status' => 'error',
            'message' => 'Payload cannot be empty.'
        ];
    }
}

// Create an instance of the API class
$api = new API();

// Handle the request based on the method
switch ($request_method) {
    case 'GET':
        $response = $api->httpGet($received_data);
        break;
    case 'POST':
        $response = $api->httpPost($received_data);
        break;
    case 'PUT':
        $response = $api->httpPut($ids, $received_data);
        break;
    case 'DELETE':
        $response = $api->httpDelete($ids, $received_data);
        break;
    default:
        $response = [
            'method' => 'UNKNOWN',
            'status' => 'error',
            'message' => 'Invalid request method.'
        ];
        break;
}

// Send the response back to the client
header('Content-Type: application/json');
echo json_encode($response);

?>
