import React, { useState } from 'react';
import * as yup from 'yup';
import { useFormik } from 'formik';
import { Button, Stack, Alert, AlertTitle,InputAdornment,IconButton } from '@mui/material';
import { Link, useParams } from 'react-router-dom';
import CustomTextField from '../../components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '../../components/forms/theme-elements/CustomFormLabel';
import { IconEye, IconEyeOff } from '@tabler/icons';
import axios from 'axios';

const validationSchema = yup.object({
  newPassword: yup
    .string()
    .required('New Password is required')
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
      'Password must contain at least one lowercase letter, one uppercase letter, one digit, one special character, and be at least 8 characters long'
    ),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('newPassword')], 'Confirm Password should match with New Password')
    .required('Confirm Password is required'),
});

const AuthResetPassword = () => {
  const [isSuccessVisible, setIsSuccessVisible] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [isErrorVisible, setIsErrorVisible] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  // Use useParams to get the token from the URL
  const { token } = useParams<{ token: string }>();

  const formik = useFormik({
    initialValues: {
      newPassword: '',
      confirmPassword: '',
    },
    validationSchema: validationSchema,
    onSubmit: async (values) => {
      try {
        const formData = new FormData();
        formData.append('new_password', values.newPassword);
        formData.append('con_password', values.confirmPassword);
        formData.append('token', token);

        const appUrl = import.meta.env.VITE_API_URL;
        const API_URL = appUrl + '/api/reset-password';
        const response = await axios.post(`${API_URL}`, formData);

        if (response.status === 200) {
          console.log('ResetPassword success:', response.data);
          setIsSuccessVisible(true);
          if (response.data && response.data.message) {
            setSuccessMessage(response.data.message);
          }
          formik.resetForm();
        }
      } catch (error) {
        console.error('ResetPassword failed:', error);
        setIsErrorVisible(true);
        if (error.response && error.response.data && error.response.data.message) {
          setErrorMessage(error.response.data.message);
        } else {
          setErrorMessage('An error occurred while resetting the password.');
        }
      }
    },
  });
  //for new password
  const [showPassword2, setShowPassword2] = useState(false);
  const handleClickShowPassword2 = () => setShowPassword2((show) => !show);
  const handleMouseDownPassword2 = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
  };

  //for confirm password
  const [showPassword3, setShowPassword3] = useState(false);
  const handleClickShowPassword3 = () => setShowPassword3((show) => !show);
  const handleMouseDownPassword3 = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
  };
  return (
    <>
      {isErrorVisible && (
        <Alert severity="error" onClose={() => setIsErrorVisible(false)} sx={{ mb: 2 }}>
          <AlertTitle>{errorMessage && <div>{errorMessage}</div>}</AlertTitle>
        </Alert>
      )}
      {isSuccessVisible && (
        <Alert severity="success" onClose={() => setIsSuccessVisible(false)} sx={{ mb: 2 }}>
          <AlertTitle>{successMessage && <div>{successMessage}</div>}</AlertTitle>
        </Alert>
      )}

      <Stack mt={2} spacing={2}>
        <form onSubmit={formik.handleSubmit}>
          <CustomFormLabel htmlFor="newPassword">New Password</CustomFormLabel>
          <CustomTextField
            type={showPassword2 ? 'text' : 'password'}
            id="newPassword"
            name="newPassword"
            variant="outlined"
            fullWidth
            value={formik.values.newPassword}
            onChange={formik.handleChange}
            error={formik.touched.newPassword && Boolean(formik.errors.newPassword)}
            helperText={formik.touched.newPassword && formik.errors.newPassword}
            placeholder="New Password"
            InputProps={{
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label="toggle password visibility"
                    onClick={handleClickShowPassword2}
                    onMouseDown={handleMouseDownPassword2}
                    edge="end"
                  >
                    {showPassword2 ? <IconEyeOff style={{ height: 20 }}/> : <IconEye style={{ height: 20 }}/>}
                  </IconButton>
                </InputAdornment>
              ),
            }}
          />

          <CustomFormLabel htmlFor="confirmPassword">Confirm Password</CustomFormLabel>
          <CustomTextField
            type={showPassword3 ? 'text' : 'password'}
            id="confirmPassword"
            name="confirmPassword"
            variant="outlined"
            fullWidth
            value={formik.values.confirmPassword}
            onChange={formik.handleChange}
            error={formik.touched.confirmPassword && Boolean(formik.errors.confirmPassword)}
            helperText={formik.touched.confirmPassword && formik.errors.confirmPassword}
            placeholder="Confirm Password"
            InputProps={{
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label="toggle password visibility"
                    onClick={handleClickShowPassword3}
                    onMouseDown={handleMouseDownPassword3}
                    edge="end"
                  >
                    {showPassword3 ? <IconEyeOff style={{ height: 20 }}/> : <IconEye style={{ height: 20 }}/>}
                  </IconButton>
                </InputAdornment>
              ),
            }}
          />

          <Button color="primary" variant="contained" size="large" fullWidth type="submit" sx={{ mt: 2 }}>
            Submit
          </Button>
        </form>

        <Button color="primary" size="large" fullWidth component={Link} to="/login">
          Back to Login
        </Button>
      </Stack>
    </>
  );
};

export default AuthResetPassword;