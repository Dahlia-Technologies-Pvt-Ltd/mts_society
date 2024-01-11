import React, { useState } from 'react';
import { Button, Stack, Alert, AlertTitle, CircularProgress, Box } from '@mui/material';
import { Link } from 'react-router-dom';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import axios from 'axios';

import CustomTextField from '../../components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '../../components/forms/theme-elements/CustomFormLabel';

const validationSchema = Yup.object().shape({
  email: Yup.string().email('Invalid email format').required('Email is required'),
});

const AuthForgotPassword = () => {
  const [isErrorVisible, setIsErrorVisible] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [isSuccessVisible, setIsSuccessVisible] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);


  const formik = useFormik({
    initialValues: {
      email: '',
    },
    validationSchema,
    onSubmit: async (values) => {
      try {
        setIsLoading(true);
        const formData = new FormData();
        formData.append('email_id', values.email);

        const appUrl = import.meta.env.VITE_API_URL;
        const API_URL = appUrl + '/api/forget-password';

        const response = await axios.post(`${API_URL}`, formData);
        console.log('ForgotPassword success:', response.data);
        setIsSuccessVisible(true);
        if (response.data && response.data.message) {
          setSuccessMessage(response.data.message);
        }
      } catch (error) {
        console.error('ForgotPassword failed:', error);
        setIsErrorVisible(true);
        if (error.response && error.response.data && error.response.data.message) {
          setErrorMessage(error.response.data.message);
        } else {
          setErrorMessage('An error occurred while reset the password.');
        }
      }finally{
        setIsLoading(false);
      }
    },
  });

  return (
    <>
      {/* Error Alert */}
      {isErrorVisible && (
        <Alert severity="error" onClose={() => setIsErrorVisible(false)} sx={{ mt: 2 }}>
          <AlertTitle>{errorMessage}</AlertTitle>
        </Alert>
      )}

      {/* Success Alert */}
      {isSuccessVisible && (
        <Alert severity="success" onClose={() => setIsSuccessVisible(false)} sx={{ mt: 2 }}>
          <AlertTitle>{successMessage}</AlertTitle>
        </Alert>
      )}

      <Stack spacing={2}>
        <form onSubmit={formik.handleSubmit}>
          <CustomFormLabel htmlFor="reset-email">Email Address</CustomFormLabel>
          <CustomTextField
            id="reset-email"
            name="email"
            variant="outlined"
            fullWidth
            value={formik.values.email}
            onChange={formik.handleChange}
            onBlur={formik.handleBlur}
            error={formik.touched.email && Boolean(formik.errors.email)}
            helperText={formik.touched.email && formik.errors.email}
            placeholder="Email Address"
          />

        <Button
          type="submit"
          color="primary"
          variant="contained"
          size="large"
          fullWidth
          sx={{ mt: 2 }}
          disabled={isLoading}
        >
          <Box
            display="flex"
            alignItems="center"
            justifyContent="center"
            gap={1}
          >
            Forgot Password
            {isLoading && <CircularProgress size={24} color="inherit" />}
          </Box>
        </Button>

        </form>
        <Button color="primary" size="large" fullWidth component={Link} to="/login">
          Back to Login
        </Button>
      </Stack>
    </>
  );
};

export default AuthForgotPassword;