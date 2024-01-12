// Utils.js

import axios from "axios";

// Utility function for involving secondary CRM
export const involveSecondaryCrm = async (id, escalation_id, fetchData, setEscalationStatus, setSuccessMessage, setIsSuccessVisible, setErrorMessage, setIsErrorVisible) => {
  try {
    const formData = new FormData();
    formData.append("id", id);
    const appUrl = import.meta.env.VITE_API_URL;
    const API_URL = appUrl + "/api/involve-secondary_crm";
    const token = sessionStorage.getItem("authToken");
    const response = await axios.post(API_URL, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    closeEscalation(escalation_id, fetchData, setEscalationStatus, setSuccessMessage, setIsSuccessVisible, setErrorMessage, setIsErrorVisible);
  } catch (error) {
    handleApiError(error, setErrorMessage, setIsErrorVisible);
  }
};

// Utility function for closing escalation
export const closeEscalation = async (id, fetchData, setEscalationStatus, setSuccessMessage, setIsSuccessVisible, setErrorMessage, setIsErrorVisible) => {
  try {
    const formData = new FormData();
    formData.append("escalation_id", id);
    const appUrl = import.meta.env.VITE_API_URL;
    const API_URL = appUrl + "/api/escalations-status-update";
    const token = sessionStorage.getItem("authToken");
    const response = await axios.post(API_URL, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    setEscalationStatus('1');
    setSuccessMessage(response.data.message);
    setIsSuccessVisible(true);
    fetchData();
  } catch (error) {
    handleApiError(error, setErrorMessage, setIsErrorVisible);
  }
};


// Utility function to handle API errors
export const handleApiError = (error, setErrorMessage, setIsErrorVisible) => {
  setIsErrorVisible(true);
  if (error.response && error.response.data && error.response.data.message) {
    setErrorMessage(error.response.data.message);
  } else {
    setErrorMessage("An error occurred while updating status.");
  }
};
