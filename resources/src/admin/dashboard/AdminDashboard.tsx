import React, {useState, useEffect} from 'react';
import { Box, Grid, Typography, Button, Dialog, DialogContent,DialogActions, Snackbar, Alert } from '@mui/material';
import PageContainer from '@src/components/container/PageContainer';

import WeeklyStats from '@src/components/dashboards/modern/WeeklyStats';
import YearlySales from '@src/components/dashboards/ecommerce/YearlySales';
import PaymentGateways from '@src/components/dashboards/ecommerce/PaymentGateways';
import WelcomeCard from '@src/components/dashboards/ecommerce/WelcomeCard';
import Payment from '@src/components/dashboards/ecommerce/Payment';
import SalesProfit from '@src/components/dashboards/ecommerce/SalesProfit';
import RevenueUpdates from '@src/components/dashboards/ecommerce/RevenueUpdates';
import SalesOverview from '@src/components/dashboards/ecommerce/SalesOverview';
import TotalEarning from '@src/components/dashboards/ecommerce/TotalEarning';
import ProductsSold from '@src/components/dashboards/ecommerce/ProductsSold';
import MonthlyEarnings from '@src/components/dashboards/ecommerce/MonthlyEarnings';
import ProductPerformances from '@src/components/dashboards/ecommerce/ProductPerformances';
import RecentTransactions from '@src/components/dashboards/ecommerce/RecentTransactions';

const AdminDashboard = () => {
  const societyToken = localStorage.getItem("societyToken");
  const storedSocietyArray = localStorage.getItem('societyArray');
  const societyArray = storedSocietyArray ? JSON.parse(storedSocietyArray) : [];  

  const [isSuccessModalOpen, setSuccessModalOpen] = useState(false);
  const [snackbarOpen, setSnackbarOpen] = useState(false);
  const [snackbarMessage, setSnackbarMessage] = useState('');
  const [snackbarSeverity, setSnackbarSeverity] = useState('success');
  const handleOpenSuccessModal = () => {
    setSuccessModalOpen(true);
  };
  const handleCloseSuccessModal = (society_name, society_token) => {
    localStorage.setItem("societyToken", society_token);
    setSnackbarMessage("Now you are in "+society_name+" society");
    setSnackbarOpen(true);
    setTimeout(() => {
      setSnackbarOpen(false);
    }, 5000);
    setSuccessModalOpen(false);
  };

  useEffect(() => {
    if(societyToken){
    }else{
      handleOpenSuccessModal();
    }
  }, []);
  const handleSnackbarClose = () => {
    setSnackbarOpen(false);
  };
  return (
    <PageContainer title="Admin Dashboard" description="this is Admin Dashboard page">
      <Box mt={3}>
        <Grid container spacing={3}>
          {/* column */}
          <Grid item xs={12} lg={8}>
            <WelcomeCard />
          </Grid>

          {/* column */}
          <Grid item xs={12} lg={4}>
            <Grid container spacing={3}>
              <Grid item xs={12} sm={6}>
                <Payment />
              </Grid>
              <Grid item xs={12} sm={6}>
                <ProductsSold />
              </Grid>
            </Grid>
          </Grid>
          <Grid item xs={12} sm={6} lg={4}>
            <RevenueUpdates />
          </Grid>
          <Grid item xs={12} sm={6} lg={4}>
            <SalesOverview />
          </Grid>
          <Grid item xs={12} sm={6} lg={4}>
            <Grid container spacing={3}>
              <Grid item xs={12} sm={6}>
                <TotalEarning />
              </Grid>
              <Grid item xs={12} sm={6}>
                <SalesProfit />
              </Grid>
              <Grid item xs={12}>
                <MonthlyEarnings />
              </Grid>
            </Grid>
          </Grid>
          {/* column */}
          <Grid item xs={12} sm={6} lg={4}>
            <WeeklyStats />
          </Grid>
          {/* column */}
          <Grid item xs={12} lg={4}>
            <YearlySales />
          </Grid>
          {/* column */}
          <Grid item xs={12} lg={4}>
            <PaymentGateways />
          </Grid>
          {/* column */}

          {/* <Grid item xs={12} lg={4}>
            <RecentTransactions />
          </Grid> */}
          {/* column */}

          {/* <Grid item xs={12} lg={8}>
            <ProductPerformances />
          </Grid> */}
        </Grid>
      </Box>
      {/* Success Modal */}
      <Dialog open={isSuccessModalOpen}>
        <DialogContent
          sx={{
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
          }}
        >
          <Typography variant="h6" align="center">
            Welcome Back Admin!
          </Typography>
          <Typography variant="body1" align="center" mt={1}>
            Please select your Society in which you want to proceed
          </Typography>
          <div style={{ display: "flex", flexDirection: "row", marginTop: "1rem" }}>
            {societyArray.map((society, index) => (
              <Button
                key={index} // Make sure to add a unique key when using map
                onClick={() => handleCloseSuccessModal(society.society_name, society.society_token)}
                variant="contained"
                color="secondary"
                style={{ marginRight: "1rem" }}
              >
                {society ? society.society_name : ''}
              </Button>
            ))}
          </div>
        </DialogContent>
      </Dialog>
      {/* message  */}
      <Snackbar
        open={snackbarOpen}
        autoHideDuration={6000}
        onClose={handleSnackbarClose}
        anchorOrigin={{ vertical: 'top', horizontal: 'center' }}
      >
        <Alert onClose={handleSnackbarClose} severity={snackbarSeverity} sx={{ width: '100%' }}>
          {snackbarMessage}
        </Alert>
      </Snackbar>
    </PageContainer>
  );
};

export default AdminDashboard;