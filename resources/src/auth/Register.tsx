import React from 'react';
import { Link } from 'react-router-dom';
import { Grid, Box, Stack, Typography } from '@mui/material';
import PageContainer from '@src/components/container/PageContainer';
import img5 from '@src/assets/images/backgrounds/img.png';
import AuthRegister from './authForms/AuthRegister';

const Register = () => (
  <PageContainer title="Society Register" description="this is Login page">
    <Grid container spacing={0} sx={{ overflowX: 'hidden' }}>
      <Grid
        item
        xs={12}
        sm={12}
        lg={7}
        xl={7}
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
        xl={5}
        display="flex"
        justifyContent="center"
        alignItems="center"
      >
        <Box >
          <AuthRegister />
        </Box>
      </Grid>
    </Grid>
  </PageContainer>
);

export default Register;