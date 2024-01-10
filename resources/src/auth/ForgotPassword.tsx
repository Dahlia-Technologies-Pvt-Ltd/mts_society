import React from 'react';
import { Grid, Box, Typography } from '@mui/material';
import img5 from '@src/assets/images/backgrounds/img.png';
import PageContainer from '@src/components/container/PageContainer';
import AuthForgotPassword from './authForms/AuthForgotPassword';

const ForgotPassword = () => (
  <PageContainer title="Forgot Password" description="this is Forgot Password page">
    <Grid container  spacing={0} sx={{ overflowX: 'hidden' }}>
    <Grid
        item
        xs={12}
        sm={12}
        lg={7}
        xl={8}
        sx={{
          position: 'relative',
          '&:before': {
            content: '""',
            background: 'radial-gradient(#d2f1df, #d3d7fa, #bad8f4)',
            backgroundSize: '400% 400%',
            animation: 'gradient 15s ease infinite',
            position: 'absolute',
            height: '100%',
            width: '100%',
            opacity: '0.3',
          },
        }}
      >
        <Box position="relative">
          <Box
            alignItems="center"
            justifyContent="center"
            height='100vh'
            sx={{
              display: {
                xs: 'none',
                lg: 'flex',
              },
            }}
          >
            <img
              src={img5}
              alt="bg"
              style={{
                width: '100%',
                height: '100vh',
              }}
            />
          </Box>
        </Box>
      </Grid>
      <Grid
        item
        xs={12}
        sm={12}
        lg={5}
        xl={4}
        display="flex"
        justifyContent="center"
        alignItems="center"
      >
        <Box p={7}>
          <Typography variant="h4" fontWeight="700">
            Forgot your password?
          </Typography>

          <Typography color="textSecondary" variant="subtitle2" fontWeight="400" mt={2}>
            Please enter the email address associated with your account and We will email you a link
            to reset your password.
          </Typography>
          <AuthForgotPassword />
          <footer style={{position: "fixed", bottom: 0}}>
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
        </Box>
      </Grid>
    </Grid>
  </PageContainer>
);

export default ForgotPassword;
