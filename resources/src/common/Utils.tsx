// Utils.js

import { useState } from 'react';
import { Snackbar, Alert } from '@mui/material';

//Common for showing success message and error message
export const useApiMessages = () => {
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');

    const showSuccessMessage = (message) => {
        setIsSuccessVisible(true);
        setSuccessMessage(message);
    };

    const showErrorMessage = (error) => {
        setIsErrorVisible(true);
        if(error.response && error.response.data && error.response.data.validation_error){
          const validationErrors = error.response.data.validation_error;
          const errorMessages = [];
          // Iterate through each field in the validation_errors object
          for (const field in validationErrors) {
              if (validationErrors.hasOwnProperty(field)) {
                  const errorMessage = validationErrors[field][0];
                  errorMessages.push(errorMessage);
              }
          }
          const concatenatedErrorMessage = errorMessages.join("\n");
          setErrorMessage(concatenatedErrorMessage);
        }else if (error.response && error.response.data && error.response.data.message) {
          setErrorMessage(error.response.data.message);
        } else {
          setErrorMessage('An error occurred while processing the request.');
        }
    };

    const closeMessages = () => {
        setIsSuccessVisible(false);
        setIsErrorVisible(false);
    };

    const renderSuccessMessage = () => (
        <Snackbar
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isSuccessVisible}
            autoHideDuration={3000}
            onClose={closeMessages}
            style={{ marginRight: '50px' }}
        >
            <Alert severity="success">
                <div style={{ fontSize: '14px', padding: '2px' }}>
                    {successMessage && <div>{successMessage}</div>}
                </div>
            </Alert>
        </Snackbar>
    );

    const renderErrorMessage = () => (
        <Snackbar
            anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
            open={isErrorVisible}
            autoHideDuration={3000}
            onClose={closeMessages}
            style={{ marginRight: '50px' }}
        >
            <Alert severity="error">
                <div style={{ fontSize: '14px', padding: '2px' }}>
                    {errorMessage && <div>{errorMessage}</div>}
                </div>
            </Alert>
        </Snackbar>
    );

    return { showSuccessMessage, showErrorMessage, renderSuccessMessage, renderErrorMessage };
};