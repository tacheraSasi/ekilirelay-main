# ekiliRelay SDK

## Installation | JS

To get started with `ekiliRelay` use npm to install and include it in your project.

1. **Npm**

    ```javascript
    npm i ekilirelay;
    ```


2. **Import the SDK into your project:**

    ```javascript
    import EkiliRelay from 'ekilirelay';
    ```

## Usage | JS

After including the SDK, you can start using `ekiliRelay` to send emails. Here's a quick example:

```js
  const sendBtn = document.getElementById("send"); // Some button
  

  // Initialize the SDK  
  const sdk = new EkiliRelay();

  sendBtn.addEventListener("click", () => { // Listening to a click event
    // Send an email
    sdk.sendEmail('reciver-email@example.com', 'Test Subject', 'This is a test message.', 'From: senderName <sender-email@example.com>')
      .then(response => {
        if (response.status === 'success') {
          console.log('Email sent successfully.');
        } else {
          console.log('Failed to send email: ' + response.message);
          console.log(response);
        }
      })
      .catch(error => {
        console.log('Error:', error);
      });
  });
