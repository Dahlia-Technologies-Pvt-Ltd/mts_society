import React, { useState } from 'react';
import {
  Box,
  Typography,
  Grid,
  Alert,
  AlertTitle,
  FormGroup,
  FormControlLabel,
  Button,
  Stack,
  InputAdornment,
  IconButton,
  CircularProgress,
} from '@mui/material';
import { Link, useNavigate } from 'react-router-dom';
import { useFormik } from 'formik';
import * as yup from 'yup';
import axios from 'axios'; // Import Axios for API calls

import { loginType } from '@src/types/auth/auth';
import CustomTextField from '../../components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '../../components/forms/theme-elements/CustomFormLabel';
import { IconEye, IconEyeOff } from '@tabler/icons';
import { addProfilePicture } from '@src/store/apps/eCommerce/ECommerceSlice';
import { useSelector, useDispatch } from '@src/store/Store';
import Logo from '@src/layouts/full/shared/logo/Logo1';

import Appstore from '@src/assets/images/logos/app.png';
import Playstore from '@src/assets/images/logos/play.png';

const validationSchema = yup.object({
  //email: yup.string().email('Enter a valid email').required('Employee ID or Customer ID is required'),
  email: yup.string().required('User ID / Email ID is required'),
  password: yup.string().required('Password is required'),
});

const AuthLogin = ({ title, subtitle, subtext }: loginType) => {
  const [isErrorVisible, setIsErrorVisible] = useState(false); // Set the visibility of the error message
  const [errorMessage, setErrorMessage] = useState(''); // Set the error message from response
  const [isSuccessVisible, setIsSuccessVisible] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const dispatch = useDispatch();
  const navigate = useNavigate(); // Initialize useNavigate for navigation
  const formik = useFormik({
    initialValues: {
      email: '',
      password: '',
    },
    validationSchema: validationSchema,
    onSubmit: async (values) => {
      const appUrl = import.meta.env.VITE_API_URL;
      const API_URL = appUrl + '/api/login'; // API service URL
      return false;
      try {
        setIsLoading(true);
        //Pass all the value in form data
        const formData = new FormData();
        formData.append('email_id', values.email);
        formData.append('password', values.password);

        const response = await axios.post(`${API_URL}`, formData);// API call
        console.log('Login successful:', response.data);
        if (response.status === 200) {
          // Storing data in Session Storage
          sessionStorage.setItem('authToken', response.data.data.token);
          sessionStorage.setItem('userCode', response.data.data.user_code);
          sessionStorage.setItem('userId', response.data.data.id);
          sessionStorage.setItem('userName', response.data.data.name);
          sessionStorage.setItem('userEmail', response.data.data.email_id);
          sessionStorage.setItem('userType', response.data.data.user_type);
          sessionStorage.setItem('profilePicture', response.data.data.profile_picture);
          dispatch(addProfilePicture(response.data.data.profile_picture));
          const user_role = (response.data.data.user_type == '0') ? 'Admin' : (response.data.data.user_type == '1') ? 'Resolver' : 'Customer'
          sessionStorage.setItem('userRole', user_role);
          // Navigate to another page
          if (response.data.data.user_type == '0') {
            navigate('/admin/dashboard');
          } else if (response.data.data.user_type == '1') {
            navigate('/resolver/dashboard');
          } else if (response.data.data.user_type == '2') {
            navigate('/customer/dashboard');
          }
        }
      } catch (error) {
        //display an error message to the user
        console.error('Login failed:', error);
        setIsErrorVisible(true);// set error visible true for show the Alert component
        // show error message
        if (error.response && error.response.data && error.response.data.message) {
          setIsErrorVisible(true);
          setErrorMessage(error.response.data.message);
        }
      } finally {
        setIsLoading(false);
      }
    },
  });
  if (sessionStorage.getItem('inviteMsg')) {
    const inviteMsg = sessionStorage.getItem('inviteMsg');
    setSuccessMessage(inviteMsg);
    setIsSuccessVisible(true);
    sessionStorage.removeItem('inviteMsg');
  }

  const [showPassword, setShowPassword] = React.useState(false);

  const handleClickShowPassword = () => setShowPassword((show) => !show);

  const handleMouseDownPassword = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
  };

  return (
    <Grid item paddingX='120px' overflow='hidden'>
    <form onSubmit={formik.handleSubmit}>
      <Logo />
      {title ? (
        <Typography fontWeight="700" variant="h3" mb={1}>
          {title}
        </Typography>
      ) : null}

      <Typography variant="h6"
        mt={3}
        mb={0.5}
        fontWeight={600}>MTS - Society Admin Module</Typography>

      {subtext}
      {isSuccessVisible && (
        <Alert severity="success" onClose={() => setIsSuccessVisible(false)} sx={{ mb: 2 }}>
          <AlertTitle>{successMessage && <div>{successMessage}</div>}</AlertTitle>
        </Alert>
      )}
      {isErrorVisible && (
        <Alert severity="error" onClose={() => setIsErrorVisible(false)}>
          <AlertTitle>{errorMessage && <div>{errorMessage}</div>}</AlertTitle>
        </Alert>
      )}
      <Stack>
        <Box >
          <CustomFormLabel htmlFor="email">User ID / Email ID</CustomFormLabel>
          <CustomTextField
            id="email"
            variant="outlined"
            fullWidth
            name="email"
            onChange={formik.handleChange}
            onBlur={formik.handleBlur}
            value={formik.values.email}
            error={formik.touched.email && Boolean(formik.errors.email)}
            helperText={formik.touched.email && formik.errors.email}
            placeholder="User ID / Email ID"
          />
        </Box>
        <Box>
          <CustomFormLabel htmlFor="password">Password</CustomFormLabel>
          <CustomTextField
            id="password"
            type={showPassword ? 'text' : 'password'} // Toggle between 'text' and 'password'
            variant="outlined"
            fullWidth
            name="password"
            onChange={formik.handleChange}
            onBlur={formik.handleBlur}
            value={formik.values.password}
            error={formik.touched.password && Boolean(formik.errors.password)}
            helperText={formik.touched.password && formik.errors.password}
            placeholder="Password"
            InputProps={{
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label="toggle password visibility"
                    onClick={handleClickShowPassword}
                    onMouseDown={handleMouseDownPassword}
                    edge="end"
                  >
                    {showPassword ? <IconEyeOff style={{ height: 20 }} /> : <IconEye style={{ height: 20 }} />}
                  </IconButton>
                </InputAdornment>
              ),
            }}
          />
        </Box>
        <Stack justifyContent="space-between" direction="row" alignItems="center" my={2}>
          <Typography
            component={Link}
            to="/forgot-password"
            fontWeight="500"
            sx={{
              textDecoration: 'none',
              color: 'primary.main',
            }}
          >
            Forgot Password?
          </Typography>
        </Stack>
      </Stack>
      <Box>
        <Button
          color="primary"
          variant="contained"
          size="large"
          fullWidth
          type="submit"
          disabled={isLoading}
        >
          Sign In
          {isLoading && <CircularProgress size={24} color="inherit" />}
        </Button>
      </Box>

      <Stack>
        <Grid
        item
        md={8}
        display='flex'
        alignItems='center'
        justifyContent='center'>
          <div>
            <a href="https://play.google.com/store/games?hl=en_IN&gl=US" target='_blank'>
           <img
              src={Playstore}
              alt="bg"
              style={{
                width: '80%',
              }}
            /></a></div>
            <div>
            <a href="https://www.apple.com/in/app-store/" target='_blank'>
            <img
              src={Appstore}
              alt="bg"
              style={{
                width: '80%',
              }}
            /></a></div>

        </Grid>
      </Stack>

      <Stack justifyContent="space-between" direction="row" alignItems="center" my={2}>

        <footer style={{ position: "fixed", bottom: 0 }}>
          <a href="#" target='_blank'>
            <Typography
              fontWeight="500"
              sx={{
                textDecoration: 'none',
                color: 'primary.main',
              }}
            >
              Copyright © MTS Society 2024
            </Typography></a>
        </footer>
      </Stack>
      {subtitle}
    </form>
    </Grid>
  );
};

export default AuthLogin;