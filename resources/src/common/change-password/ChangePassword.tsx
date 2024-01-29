import React, { useState } from 'react';
import * as yup from 'yup';
import { useFormik } from 'formik';
import CustomTextField from '@src/components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '@src/components/forms/theme-elements/CustomFormLabel';
import ParentCard from '@src/components/shared/ParentCard';
import PageContainer from '@src/components/container/PageContainer';
import Breadcrumb from '@src/layouts/full/shared/breadcrumb/Breadcrumb';
import { FormControlLabel, Button, Alert, AlertTitle, Stack, Box, Typography, 
  InputAdornment,IconButton } from '@mui/material';
import { IconEye, IconEyeOff } from '@tabler/icons';
import axios from 'axios';

const BCrumb = [
  {
    to: '/',
    title: 'Home',
  },
  {
    title: 'Change Password',
  },
];

const validationSchema = yup.object({
  curr_password: yup.string().required('Current Password is required'),
  new_password: yup
    .string()
    .required('New Password is required')
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
      'Password must contain at least one lowercase letter, one uppercase letter, one digit, one special character, and be at least 8 characters long'
    ),
  confirm_password: yup
    .string()
    .oneOf([yup.ref('new_password')], 'Confirm Password did not match with New Password')
    .required('Confirm Password is required'),
});

const ChangePassword = () => {
  const [isSuccessVisible, setIsSuccessVisible] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [isErrorVisible, setIsErrorVisible] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  React.useEffect(() => {
    if (isErrorVisible) {
      const timeout = setTimeout(() => {
        setIsErrorVisible(false);
      }, 5000); // Adjust the time in milliseconds (5 seconds in this example)

      return () => {
        clearTimeout(timeout);
      };
    }
  }, [isErrorVisible]);
  const formik = useFormik({
    initialValues: {
      curr_password: '',
      new_password: '',
      confirm_password: '',
    },
    validationSchema: validationSchema,
    onSubmit: async (values) => {

      const appUrl = import.meta.env.VITE_API_URL;
      const API_URL = appUrl + '/api/change-password';
      try {
        const formData = new FormData();
        formData.append('curr_password', values.curr_password);
        formData.append('new_password', values.new_password);
        formData.append('confirm_password', values.confirm_password);
        // Retrieving data from Session Storage
        const token = localStorage.getItem('authToken');
        const response = await axios.post(API_URL, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        if (response.status === 200) {
          setIsSuccessVisible(true);
          setSuccessMessage('Password changed successfully.');
          formik.resetForm();
        }
      } catch (error) {
        setIsErrorVisible(true);
        if (error.response && error.response.data && error.response.data.validation_error) {
          const validationErrors = error.response.data.validation_error;
        const errorMessages = [];

        // Iterate through each field in the validation_errors object
        for (const field in validationErrors) {
          if (validationErrors.hasOwnProperty(field)) {
            // Get the error message for the field
            const errorMessage = validationErrors[field][0];
  
            // Add the error message to the array
            errorMessages.push(errorMessage);
          }
          // Now, you have an array (errorMessages) that contains all error messages.
          // You can concatenate them into a single message, separated by line breaks or commas.
          const concatenatedErrorMessage = errorMessages.join('\n');
          setErrorMessage(concatenatedErrorMessage);
        }
        }
        else if(error.response.data.message) {
          setErrorMessage(error.response.data.message);
        } else {
          setErrorMessage('An error occurred while changing the password.');
        }
      }
    },
  });

  //for Current password
  const [showPassword, setShowPassword] = useState(false);
  const handleClickShowPassword = () => setShowPassword((show) => !show);
  const handleMouseDownPassword = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
  };

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
    <PageContainer title="Change Password" description="This is the Change Password page">
      <Breadcrumb title="Change Password" items={BCrumb} />

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

      <ParentCard title="Change Password">
        <form onSubmit={formik.handleSubmit}>
          <CustomFormLabel sx={{ mt: 0 }} htmlFor="ordinary-outlined-password-input">
            Current Password
          </CustomFormLabel>

          <CustomTextField
            id="ordinary-outlined-password-input"
            name="curr_password"
            placeholder="Current Password"
            type={showPassword ? 'text' : 'password'}
            autoComplete="current-password"
            variant="outlined"
            sx={{
              mb: '10px',
              width:'50%',
            }}
            value={formik.values.curr_password}
            onChange={formik.handleChange}
            error={formik.touched.curr_password && Boolean(formik.errors.curr_password)}
            helperText={formik.touched.curr_password && formik.errors.curr_password}
            InputProps={{
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label="toggle password visibility"
                    onClick={handleClickShowPassword}
                    onMouseDown={handleMouseDownPassword}
                    edge="end"
                  >
                    {showPassword ? <IconEyeOff style={{ height: 20 }}/> : <IconEye style={{ height: 20 }}/>}
                  </IconButton>
                </InputAdornment>
              ),
            }}
          />

          <CustomFormLabel htmlFor="ordinary-outlined-password-input2">
            New Password
          </CustomFormLabel>

          <CustomTextField
            id="ordinary-outlined-password-input2"
            name="new_password"
            placeholder="New Password"
            type={showPassword2 ? 'text' : 'password'}
            autoComplete="new-password"
            variant="outlined"
            sx={{
              mb: '10px',
              width:'50%',
            }}
            value={formik.values.new_password}
            onChange={formik.handleChange}
            error={formik.touched.new_password && Boolean(formik.errors.new_password)}
            helperText={formik.touched.new_password && formik.errors.new_password}
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

          <CustomFormLabel htmlFor="ordinary-outlined-password-input3">
            Confirm Password
          </CustomFormLabel>

          <CustomTextField
            id="ordinary-outlined-password-input3"
            name="confirm_password"
            type={showPassword3 ? 'text' : 'password'}
            placeholder="Confirm Password"
            autoComplete="confirm-password"
            variant="outlined"
            sx={{
              mb: '10px',
              width:'50%',
            }}
            value={formik.values.confirm_password}
            onChange={formik.handleChange}
            error={formik.touched.confirm_password && Boolean(formik.errors.confirm_password)}
            helperText={formik.touched.confirm_password && formik.errors.confirm_password}
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

          <div>
            <Button color="primary" variant="contained" type="submit">
              Submit
            </Button>
          </div>
        </form>
      </ParentCard>
    </PageContainer>
  );
};

export default ChangePassword;