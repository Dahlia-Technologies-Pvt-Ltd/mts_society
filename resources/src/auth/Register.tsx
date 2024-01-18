import React from 'react';
import { Grid, Box, Card, Typography } from '@mui/material';
import { Link } from 'react-router-dom';
import PageContainer from '@src/components/container/PageContainer';
import Logo from '@src/layouts/full/shared/logo/Logo';
import AuthRegister from '@src/auth/authForms/AuthRegister';
import img5 from '@src/assets/images/backgrounds/img.png';

const Register = () => (
  <PageContainer title="Register" description="This is the Register page">
    <Box
      sx={{
        position: 'relative',
        backgroundImage: `url(${img5})`, // Set the background image
        //backgroundSize: 'cover', // You can adjust this based on your needs
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
      <Grid container spacing={0} justifyContent="center" sx={{ height: '100vh' }}>
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
          <Card elevation={9} sx={{ p: 2, zIndex: 1, width: '100%', maxWidth: '450px', backgroundColor: 'rgba(255, 255, 255, 1)' }}>
            <Box display="flex" alignItems="center" justifyContent="center">
              <Logo />
            </Box>
            <Typography variant="h6" textAlign="center" color="textSecondary" mb={1}>
              Create an Account
            </Typography>
            <AuthRegister />
          </Card>
        </Grid>
      </Grid>
    </Box>
  </PageContainer>
);

export default Register;
