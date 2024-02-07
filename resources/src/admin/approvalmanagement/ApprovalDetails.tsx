import React, { useEffect, useState } from "react";
import Breadcrumb from '@src/layouts/full/shared/breadcrumb/Breadcrumb';
import PageContainer from '@src/components/container/PageContainer';
import ParentCard from '@src/components/shared/ParentCard';
import CustomFormLabel from '@src/components/forms/theme-elements/CustomFormLabel';
import CustomTextField from '@src/components/forms/theme-elements/CustomTextField';
import axios from "axios";
import { Link, useParams, useNavigate } from 'react-router-dom';
import { Divider, CircularProgress } from '@mui/material';
import {
  Grid,
  Box,
  Typography,
  FormControl,
  MenuItem,
  RadioGroup,
  FormControlLabel,
  Button,
  SliderValueLabelProps,
  Stack,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  CardHeader,
  Paper,
  useTheme,
} from '@mui/material';

const BCrumb = [
  {
    to: '/admin/approval-list',
    title: 'Approval management',
  },
  {
    title: 'Approval User Details',
  },
];

const ApprovalDetails = () => {

  const { id } = useParams(); // Get the 'id' parameter from the URL
  const [fetchedData, setFetchedData] = useState(null);
  const [error, setError] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const theme = useTheme();
  const appUrl = import.meta.env.VITE_API_URL;
  const society_token = localStorage.getItem("societyToken");
  const navigate = useNavigate();
  useEffect(() => {
    const fetchData = async () => {
      const apiUrl = `${appUrl}/api/show-user-for-approval`;
      try {
        const token = localStorage.getItem('authToken');
        const formData = new FormData();
        formData.append("id", id);
        const response = await axios.post(apiUrl, formData, {
          headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "multipart/form-data",
              "society_id": `${society_token}`,
          },
      });

        if (response && response.data && response.data.data) {
          setFetchedData(response.data.data);
        } else {
          console.error("Error: Unexpected response structure", response);
        }
        //console.log(response.data);
      } catch (error) {
        setError(error.response.data.message);
        //console.log('Error',error.response.data.message);
      }
    };

    fetchData();
  }, [id]);

  const handleApprove = async () => {
    const API_URL = `${appUrl}/api/user-for-approval`;
    setIsLoading(true);
    // Create a FormData object
    const formData = new FormData();
    formData.append("master_user_id", id);
    try {
      const token = localStorage.getItem('authToken');
      const response = await axios.post(API_URL, formData, {
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
            "society_id": `${society_token}`,
        },
    });
    sessionStorage.setItem("successMessage", response.data.message);
    navigate("/admin/approval-list");
    } catch (error) {
      setError(error.response.data.message);
      //console.log('Error',error.response.data.message);
    }finally{
      setIsLoading(false);
    }
  }
  const customButton = (
    <Link to="/admin/approval-list"><Button variant="contained" color="primary">
    Back
  </Button></Link>
  );
  return (
    <PageContainer title="Approval Details" description="this is Approval Details page">
      {/* breadcrumb */}
      <Breadcrumb title="" items={BCrumb} />
      {/* end breadcrumb */} 
  
      <ParentCard title="Approval User Details" button={customButton}>
        <Grid container spacing={3}>
          <Grid item xs={12} sm={12} lg={12}>
            <Grid container spacing={3} pt={1}>
              <Grid item xs={12} sm={12} lg={6} mt={2} pb={3}>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Name</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.name ? fetchedData.name : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Unique Code</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.user_code ? fetchedData.user_code : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Phone Number</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.phone_number ? fetchedData.phone_number : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Email ID</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.email ? fetchedData.email : '-'}  </Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">User Type</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.usertype == '0' ? 'User' : '-'}</Typography>
                  </Grid>
                </Grid>
              </Grid>
              <Grid item xs={12} sm={12} lg={6} pb={3}>
                <Typography fontWeight={600} mb={2} variant="h6"></Typography>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Country</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.country ? fetchedData.country.name : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">State</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.state ? fetchedData.state.name : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">City</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.city ? fetchedData.city : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Zipcode</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.zipcode ? fetchedData.zipcode : '-'}</Typography>
                  </Grid>
                </Grid>
                <Grid container spacing={3} mb={2}>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2" color="grey.400">Created On</Typography>
                  </Grid>
                  <Grid item xs={6} sm={6} lg={6}>
                    <Typography fontWeight={100} variant="subtitle2">{fetchedData && fetchedData.created_at ? fetchedData.created_at : '-'}</Typography>
                  </Grid>
                </Grid>
              </Grid>
            </Grid>
          </Grid>
          {/* Submit Button */}
          <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
              <Link to="/admin/approval-list">
                  <Button
                      color="warning"
                      variant="contained"
                      style={{ marginRight: "10px" }}
                  >
                      Back
                  </Button>
              </Link>
              <Button
                variant="contained"
                color="success"
                type="submit"
                disabled={isLoading}
                onClick = {handleApprove}
              >
                Approve
                {isLoading && <CircularProgress size={24} color="inherit" />}
              </Button>
          </Grid>
        </Grid>
      </ParentCard>
    </PageContainer>
  );
};

export default ApprovalDetails;