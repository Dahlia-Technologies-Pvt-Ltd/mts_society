import React, { useState, useEffect }from 'react';
import { Box, Typography, Button, Divider, Stack,
  Stepper,
  Step,
  StepLabel,
  FormControlLabel,
  Alert,
  Grid,
  MenuItem,
  Select,
  Autocomplete,
  Radio, RadioGroup,FormControl, FormLabel,CardContent,
  Snackbar,
} from '@mui/material';
import { Link } from 'react-router-dom';
import PageContainer from '@src/components/container/PageContainer';
import CustomTextField from '@src/components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '@src/components/forms/theme-elements/CustomFormLabel';
import CustomCheckbox from '@src/components/forms/theme-elements/CustomCheckbox';
import ParentCard from '@src/components/shared/ParentCard';
import Logo from '@src/layouts/full/shared/logo/Logo1';
import { useFormik } from 'formik';
import * as yup from 'yup';
import axios from 'axios'; // Import Axios for API calls

const steps = ['Account', 'Society', 'Subscription'];

const AuthRegister = () => {
  const [activeStep, setActiveStep] = React.useState(0);
  const [validActiveStep, setValidActiveStep] = useState(0);
  const [skipped, setSkipped] = React.useState(new Set());
  const [snackbarOpen, setSnackbarOpen] = useState(false);
  const [snackbarMessage, setSnackbarMessage] = useState('');
  const [snackbarSeverity, setSnackbarSeverity] = useState('success');
  const [countryOptions, setCountryData] = useState([]);
  const [stateOptions, setStateData] = useState([]);
  const [topCards, setMasterSubscriptionData] = useState([]);
  const appUrl = import.meta.env.VITE_API_URL;

  useEffect(() => {
    fetchCountry();
    fetchMasterSubscription();
  }, []);

  // Function to fetch data from the API
  const fetchCountry = async () => {
    try {
        const formData = new FormData();
        formData.append("sortBy", 'name');
        formData.append('sortOrder', 'asc');
        const API_URL = appUrl + '/api/list-country';
        const response = await axios.post(API_URL, formData);
        //console.log(JSON.stringify(response.data.data.data));
        if (response && response.data && response.data.data) {
          setCountryData(response.data.data.data);
        }
    } catch (error) {
        console.error("Error fetching data:", error); // Log any errors
    }
  };

  // Function to fetch data from the API
  const fetchState = async (country_id) => {
    try {
        const formData = new FormData();
        formData.append("sortBy", 'id');
        formData.append('sortOrder', 'asc');
        formData.append('country_id', country_id);
        const API_URL = appUrl + '/api/list-state';
        const response = await axios.post(API_URL, formData);
        //console.log(JSON.stringify(response.data.data.data));
        if (response && response.data && response.data.data) {
          setStateData(response.data.data.data);
        }else{
          setStateData([]);
          formik.setFieldValue('state', '');
        }
    } catch (error) {
        console.error("Error fetching data:", error); // Log any errors
    }
  };

  // Function to fetch data from the API
  const fetchMasterSubscription = async () => {
    try {
        const formData = new FormData();
        formData.append("sortBy", 'id');
        formData.append('sortOrder', 'asc');
        const API_URL = appUrl + '/api/list-master-subscription';
        const response = await axios.post(API_URL, formData);
        //console.log(JSON.stringify(response.data.data.data));
        if (response && response.data && response.data.data) {
          setMasterSubscriptionData(response.data.data.data);
        }else{
          setMasterSubscriptionData([]);
        }
    } catch (error) {
        console.error("Error fetching data:", error); // Log any errors
    }
  };

  const isStepOptional = (step:any) => step === '';
  const isStepSkipped = (step:any) => skipped.has(step);
  // Form validation schema using yup
  const validationSchema = [
    // Validation schema for Step 1
    yup.object({
      fullName: yup.string().required('Full Name is required'),
      phone_number: yup.string().required('Mobile Number is required'),
      email: yup.string().email('Invalid email address').required('Email Address is required'),
      password: yup.string().required('Password is required'),
    }),
    // Validation schema for Step 2
    yup.object({
      society_name: yup.string().required('Society Name is required'),
      address: yup.string().required('Address is required'),
      country: yup.mixed().required('Country is required'),
      state: yup.mixed().required('State is required'),
    }),
    // ... Add validation schemas for other steps
  ];

  const handleNext = () => {
    let newSkipped = skipped;
    // Validate the form using formik
    formik.handleSubmit();
    if (isStepSkipped(activeStep)) {
      newSkipped = new Set(newSkipped.values());
      newSkipped.delete(activeStep);
    }
    // Check if the form is valid before proceeding to the next step
    
  };

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
    setValidActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  const handleSkip = () => {
    if (!isStepOptional(activeStep)) {
      // You probably want to guard against something like this,
      // it should never occur unless someone's actively trying to break something.
      throw new Error("You can't skip a step that isn't optional.");
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped((prevSkipped) => {
      const newSkipped = new Set(prevSkipped.values());
      newSkipped.add(activeStep);

      return newSkipped;
    });
  };
  //Register Implimentation starts here
  const [selectedCountry, setSelectedCountry] = useState(null);
  const [selectedState, setSelectedState] = useState(null);
  const [selectedPlan, setSelectedPlan] = useState('1');

  const handleCountryChange = (event, newValue) => {
    //console.log('county_id', newValue);
    setSelectedCountry(newValue);
    if(newValue){
      fetchState(newValue.id);
      formik.setFieldValue('country', newValue.id);
    }else{
      setStateData([]);
      formik.setFieldValue('state', '');
      setSelectedState(null);
    }
  };
  const handleStateChange = (event, newValue) => {
    setSelectedState(newValue);
    formik.setFieldValue('state', newValue.id);
  };
  const handlePlanChange = (event) => {
    setSelectedPlan(event.target.value);
  };

  // Formik form management
  const formik = useFormik({
    initialValues: {
      fullName: '',
      phone_number: '',
      email: '',
      password: '',
      society_name: '',
      address: '',
      country: '',
      state: '',
      city: '',
      pincode: '',
    },
    validationSchema: validationSchema[validActiveStep],
    onSubmit: async (values) => {
      // Handle submission logic
      if (formik.isValid) {
        setActiveStep((prevActiveStep) => prevActiveStep + 1);
        setValidActiveStep((prevActiveStep) => prevActiveStep + 1);
      } else {
        // Handle validation errors or take appropriate action
        console.log('Form is not valid');
      }
      if(activeStep === steps.length - 1){
        try {
          // Make API request here
          const formData = new FormData();
          formData.append("name", values.fullName);
          formData.append('email', values.email);
          formData.append('phone_number', values.phone_number);
          formData.append('password', values.password);
          formData.append('society_name', values.society_name);
          formData.append('address', values.address);
          formData.append('country_id', values.country);
          formData.append('city', values.city);
          formData.append('state_id', values.state);
          formData.append('zipcode', values.pincode);
          formData.append('master_subscription_id', selectedPlan);

          const API_URL = appUrl + '/api/register';
          const response = await axios.post(API_URL, formData);

          setSnackbarMessage('Congratulations!! Your account is created successfully. please click on login button to proceed further.');
          setSnackbarSeverity('success');
          setSnackbarOpen(true);
        } catch (error) {
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
          }
          const concatenatedErrorMessage = errorMessages.join("\n");
          console.error('Error submitting form:', error);
          setSnackbarMessage(concatenatedErrorMessage);
          setSnackbarSeverity('error');
          setSnackbarOpen(true);
        }
      }
    },
  });

  const handleSnackbarClose = () => {
    setSnackbarOpen(false);
  };

  // eslint-disable-next-line consistent-return
  const handleSteps = (step: any) => {
    switch (step) {
      case 0:
        return (
          <Box>
            <Grid container spacing={1}>
              <CustomFormLabel htmlFor="fullName">Full Name<span style={{ color: 'red' }}>*</span></CustomFormLabel>
              <CustomTextField
                id="fullName"
                placeholder="Full Name"
                variant="outlined"
                fullWidth
                value={formik.values.fullName}
                onChange={formik.handleChange}
                error={formik.touched.fullName && Boolean(formik.errors.fullName)}
                helperText={formik.touched.fullName && formik.errors.fullName}
                onBlur={formik.handleBlur}
              />

              <CustomFormLabel htmlFor="phone_number">Mobile Number<span style={{ color: 'red' }}>*</span></CustomFormLabel>
              <CustomTextField
                id="phone_number"
                placeholder="Mobile Number"
                type="phone_number"
                variant="outlined"
                fullWidth
                value={formik.values.phone_number}
                onChange={formik.handleChange}
                error={formik.touched.phone_number && Boolean(formik.errors.phone_number)}
                helperText={formik.touched.phone_number && formik.errors.phone_number}
                onBlur={formik.handleBlur}
                onKeyDown={(e) => {
                  // Allow only numeric key presses
                  if (e.key === 'e' || e.key === '-' || e.key === '+' || isNaN(Number(e.key))) {
                    e.preventDefault();
                  }
                }}
              />

              <CustomFormLabel htmlFor="email">Email Address<span style={{ color: 'red' }}>*</span></CustomFormLabel>
              <CustomTextField
                id="email"
                placeholder="Email Address"
                type="email"
                variant="outlined"
                fullWidth
                value={formik.values.email}
                onChange={formik.handleChange}
                error={formik.touched.email && Boolean(formik.errors.email)}
                helperText={formik.touched.email && formik.errors.email}
                onBlur={formik.handleBlur}
              />
              <CustomFormLabel htmlFor="password">Password<span style={{ color: 'red' }}>*</span></CustomFormLabel>
              <CustomTextField
                id="password"
                placeholder="Password"
                type="password"
                variant="outlined"
                fullWidth
                value={formik.values.password}
                onChange={formik.handleChange}
                error={formik.touched.password && Boolean(formik.errors.password)}
                helperText={formik.touched.password && formik.errors.password}
                onBlur={formik.handleBlur}
              />
            </Grid>
          </Box>
        );
      case 1:
        return (
          <Box>
            <CustomFormLabel htmlFor="society_name">Society Name<span style={{color:"red"}}>*</span></CustomFormLabel>
            <CustomTextField 
              id="society_name"
              placeholder="Society Name"
              variant="outlined"
              fullWidth
              value={formik.values.society_name}
              onChange={formik.handleChange}
              error={formik.touched.society_name && Boolean(formik.errors.society_name)}
              helperText={formik.touched.society_name && formik.errors.society_name}
              onBlur={formik.handleBlur}
            />
            <CustomFormLabel htmlFor="address">Address<span style={{color:"red"}}>*</span></CustomFormLabel>
            <CustomTextField id="address" placeholder="Address" multiline rows={1} variant="outlined" fullWidth 
              value={formik.values.address}
              onChange={formik.handleChange}
              error={formik.touched.address && Boolean(formik.errors.address)}
              helperText={formik.touched.address && formik.errors.address}
              onBlur={formik.handleBlur}
            />
            {/* Country, State, City */}
            <Grid container spacing={1}>
              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="country">Country<span style={{color:"red"}}>*</span></CustomFormLabel>
                <Autocomplete
                  id="country"
                  fullWidth
                  options={countryOptions}
                  getOptionLabel={(option) => option.name}
                  isOptionEqualToValue={(option, value) => option.id === value.id}
                  value={selectedCountry}
                  onChange={handleCountryChange}
                  renderOption={(props, option) => (
                    <MenuItem component="li" {...props}>
                      {option.name}
                    </MenuItem>
                  )}
                  renderInput={(params) => (
                    <CustomTextField
                      {...params}
                      placeholder="Select"
                      aria-label="Country"
                      autoComplete="off"
                      inputProps={{
                        ...params.inputProps,
                        autoComplete: 'new-password', // disable autocomplete and autofill
                      }}
                      // Add error and helperText props based on Formik validation
                      error={formik.touched.country && Boolean(formik.errors.country)}
                      helperText={formik.touched.country && formik.errors.country}
                    />
                  )}
                />
              </Grid>

              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="state">State<span style={{color:"red"}}>*</span></CustomFormLabel>
                <Autocomplete
                  id="state"
                  fullWidth
                  options={stateOptions}
                  getOptionLabel={(option) => option.state_name}
                  isOptionEqualToValue={(option, value) => option.id === value.id}
                  value={selectedState}
                  onChange={handleStateChange}
                  renderOption={(props, option) => (
                    <MenuItem component="li" {...props}>
                      {option.state_name}
                    </MenuItem>
                  )}
                  renderInput={(params) => (
                    <CustomTextField
                      {...params}
                      placeholder="Select"
                      aria-label="State"
                      autoComplete="off"
                      inputProps={{
                        ...params.inputProps,
                        autoComplete: 'new-password', // disable autocomplete and autofill
                      }}
                      // Add error and helperText props based on Formik validation
                      error={formik.touched.state && Boolean(formik.errors.state)}
                      helperText={formik.touched.state && formik.errors.state}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="city">City</CustomFormLabel>
                <CustomTextField placeholder="City" id="city" type="text" variant="outlined" fullWidth 
                  value={formik.values.city}
                  onChange={formik.handleChange}
                  error={formik.touched.city && Boolean(formik.errors.city)}
                  helperText={formik.touched.city && formik.errors.city}
                  onBlur={formik.handleBlur}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="pincode">Pincode</CustomFormLabel>
                <CustomTextField placeholder="Pincode" id="pincode" type="text" variant="outlined" fullWidth 
                  onKeyDown={(e) => {
                    // Allow only numeric key presses
                    if (e.key === 'e' || e.key === '-' || e.key === '+' || isNaN(Number(e.key))) {
                      e.preventDefault();
                    }
                  }}
                  value={formik.values.pincode}
                  onChange={formik.handleChange}
                  error={formik.touched.pincode && Boolean(formik.errors.pincode)}
                  helperText={formik.touched.pincode && formik.errors.pincode}
                  onBlur={formik.handleBlur}
                />
              </Grid>
            </Grid>
          </Box>
        );
      case 2:
        return (
          <Box pt={2} mb={4}>
            <Grid container spacing={2}>
              {topCards.map((topcard, i) => (
                <Grid item xs={12} sm={4} lg={6} key={i}>
                  <Box textAlign="center" 
                    borderRadius={1} // Set the border radius
                    border="1px solid black" // Set the border color and width
                  >
                    <CardContent>
                      <RadioGroup
                        aria-label={`select-plan-${i}`}
                        name={`select-plan-${i}`}
                        value={selectedPlan}
                        onChange={handlePlanChange}
                      >
                        <FormControlLabel
                          value={topcard.id}
                          control={<Radio/>}
                          label={
                            <>
                              <Typography mt={1} variant="subtitle1" fontWeight={600}>
                                {topcard.subscription_plan}
                              </Typography>
                              <Typography fontSize={12} variant="subtitle1">
                                {`\u20B9${topcard.price}`} <br/>per month
                              </Typography>
                            </>
                          }
                        />
                      </RadioGroup>
                    </CardContent>
                  </Box>
                </Grid>
              ))}
            </Grid>
          </Box>
        );
      default:
        break;
    }
  };

  const handleReset = () => {
    setActiveStep(0);
  };

  return (
    <Grid item paddingX='120px' overflow='hidden'>
      {/* <Logo /> */}
      <Typography variant="h3"
        mb={4}
        fontWeight={600}>MTS - Society Register
      </Typography>
      <Stack>
        <Box width="100%" component="form" onSubmit={formik.handleSubmit}>
          <Stepper activeStep={activeStep}>
            {steps.map((label, index) => {
              const stepProps: { completed?: boolean } = {};
              const labelProps: {
                optional?: React.ReactNode;
              } = {};
              if (isStepOptional(index)) {
                labelProps.optional = <Typography variant="caption">Optional</Typography>;
              }
              if (isStepSkipped(index)) {
                stepProps.completed = false;
              }

              return (
                <Step key={label} {...stepProps}>
                  <StepLabel {...labelProps}>{label}</StepLabel>
                </Step>
              );
            })}
          </Stepper>
          {activeStep === steps.length ? (
            <>
              <Stack spacing={2} mt={3}>
                <Alert severity="success">
                  <Typography variant="h6" p={2} fontWeight={400}>
                    Congratulations!!<br/>
                    Your account is created successfully. please click on login button to proceed further.
                  </Typography>
                </Alert>

                <Box textAlign="right">
                  <Link to='/login'>
                    <Button variant="contained" color="secondary">
                      Login
                    </Button>
                  </Link>
                </Box>
              </Stack>
            </>
          ) : (
            <>
              <Box>{handleSteps(activeStep)}</Box>

              <Box display="flex" flexDirection="row" mt={3}>
                <Button
                  color="inherit"
                  variant="contained"
                  disabled={activeStep === 0}
                  onClick={handleBack}
                  sx={{ mr: 1 }}
                >
                  Back
                </Button>
                <Box flex="1 1 auto" />
                {isStepOptional(activeStep) && (
                  <Button color="inherit" onClick={handleSkip} sx={{ mr: 1 }}>
                    Skip
                  </Button>
                )}
                
                <Button
                  onClick={handleNext}
                  variant="contained"
                  color={activeStep === steps.length - 1 ? 'primary' : 'secondary'}
                >
                  {activeStep === steps.length - 1 ? 'Submit' : 'Next'}
                </Button>
              </Box>
            </>
          )}
        </Box>
      </Stack>
      <Stack direction="row" spacing={1} mt={3}>
        <Typography color="textSecondary" variant="h6" fontWeight="400">
          Already have an Account?
        </Typography>
        <Typography
          component={Link}
          to="/login"
          fontWeight="500"
          sx={{
            textDecoration: 'none',
            color: 'primary.main',
          }}
        >
          Sign In
        </Typography>
      </Stack>
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
    </Grid>
  );
};

export default AuthRegister;
