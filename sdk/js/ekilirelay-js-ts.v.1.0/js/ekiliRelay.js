/**
 * The EkiliRelay class is designed to handle email sending functionality
 * using a provided API key. It connects to a remote API endpoint and sends
 * email requests based on the given parameters.
 * 
 * This class should be initialized with an API key which is used for
 * authenticating requests to the email service.
 */
class EkiliRelay {
    /**
     * Constructs an instance of the EkiliRelay class.
     * @param {string} apikey - The API key required for authenticating
     * requests to the email service.
     */
    constructor(apikey) {
        // Store the API key for later use in API requests
        this.apikey = apikey;
        // Define the URL of the API endpoint where email requests will be sent
        this.apiUrl = "https://relay.ekilie.com/api/index.php";
        // Log a message to the console indicating the successful connection
        console.log("EkiliRelay connected");
    }

    /**
     * Sends an email using the provided details.
     * 
     * @param {string} to - The recipient's email address.
     * @param {string} subject - The subject of the email.
     * @param {string} message - The body of the email.
     * @param {string} [headers=''] - Optional additional headers for the email.
     * @returns {Promise<Object>} - A promise that resolves to the result of
     * the email sending operation.
     */
    async sendEmail(to, subject, message, headers = '') {
        // Construct the payload to be sent to the API
        const data = {
            to: to,               // Recipient's email address
            subject: subject,     // Subject line of the email
            message: message,     // Body of the email
            headers: headers,     // Optional additional headers
            apikey: this.apikey   // API key for authentication
        };

        try {
            // Send the HTTP POST request to the API endpoint with the email data
            const response = await fetch(this.apiUrl, {
                method: 'POST',                  // HTTP method to use
                headers: {
                    'Content-Type': 'application/json' // Specify that we are sending JSON data
                },
                body: JSON.stringify(data)   // Convert the data object to a JSON string
            });

            // Parse the JSON response from the server
            const result = await response.json();
            // Return the result of the email sending operation
            return result;
        } catch (error) {
            // Return an error object if something goes wrong
            return { status: 'error', message: error.message };
        }
    }
}

// Export the EkiliRelay class so it can be used in other modules
export default EkiliRelay;
