import React, { useState, useEffect } from "react";
import {
    Grid,
    FormControlLabel,
    Button,
    RadioGroup,
    Typography,
    FormGroup,
    Switch,
    Box,
    Paper,
    Alert,
    IconButton,
    AlertTitle,CircularProgress,Checkbox, Autocomplete, MenuItem
} from "@mui/material";
import { IconMinus, IconPlus, IconTrash } from '@tabler/icons';
import { useParams, Link, useNavigate } from "react-router-dom";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";
import AddIcon from '@mui/icons-material/Add';
import RemoveIcon from '@mui/icons-material/Remove';
import DeleteIcon from '@mui/icons-material/Delete';
import { useApiMessages } from '@src/common/Utils'; // Import the utility

const AddParking = () => {
    const { showSuccessMessage, showErrorMessage, renderSuccessMessage, renderErrorMessage } = useApiMessages();
    const [isLoading, setIsLoading] = useState(false);
    const [wingOption, setWingData] = useState([]);
    const [towerOption, setTowerData] = useState([]);
    const [floorOption, setFloorData] = useState([]);
    const [selectedTower, setSelectedTower] = useState(null);
    const [selectedWing, setSelectedWing] = useState(null);
    const [selectedFloor, setSelectedFloor] = useState(null);
    const [flatsData, setFlatsdata] = useState([]);
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");
    //Site Tokens
    const token = localStorage.getItem("authToken");
    const society_token = localStorage.getItem("societyToken");

    const BCrumb = [
        {
            to: "/admin/parking-list",
            title: "Parking List",
        },
        {
            title: id ? "Edit Parking" : "Add Parking",
        },
    ];

    const validationSchema = (id) => {
        return yup.object().shape({
          floor_id: yup.string().required("Floor Number is required"),
          tower_id: yup.string().required("Tower is required"),
          flatname: id ? yup.string().required("Flat is required") : yup.string(),
        });
      };
    
    const formik = useFormik({
        initialValues: {
            tower_id: "",
            wing_id: "",
            floor_id: "",
            flat_name: [""],
            flatname: "",
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("floor_id", values.floor_id);
                formData.append("tower_id", values.tower_id);
                formData.append("wing_id", values.wing_id);
                if (id) {
                    formData.append("flat_number", values.flatname);
                } else {
                    formData.append("flat_number_arr", JSON.stringify(values.flat_name));
                }

                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-flat";
                (id) ? formData.append("id", id) : '';
                // Make the API POST request
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                        "society_id": `${society_token}`,
                    },
                });
                sessionStorage.setItem("successMessage", response.data.message);
                navigate("/admin/flat-list");
            } catch (error) {
                // Handle errors here
                showErrorMessage(error);
                console.error("Edit spare part error:", error);
            }finally{
                setIsLoading(false);
            }
        },
    });

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        if (id) {
            fetchData();
        }
        fetchTower();
    }, [id]);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-flat/${id}`;
            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                    "society_id": `${society_token}`,
                },
            });

            const data = response.data.data;
            formik.setValues({
                tower_id: data.tower_id,
                wing_id: data.wing_id,
                floor_id: data.floor_id,
                flat_name: [],
                flatname: data.flat_name
            }); 
            fetchFloor(data.tower_id, data.wing_id);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };
    
    // Function to fetch data from the API
    const fetchTower = async () => {
        try {
            const formData = new FormData();
            formData.append("sortBy", 'tower_name');
            formData.append('sortOrder', 'asc');
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = appUrl + '/api/list-tower';
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "society_id": `${society_token}`,
                },
            });
            //console.log(JSON.stringify(response.data.data.data));
            if (response && response.data && response.data.data) {
                setTowerData(response.data.data.data);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        }
    };

    // Function to fetch data from the API
    const fetchWing = async (tower_id) => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-tower/${tower_id}`;
            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                    "society_id": `${society_token}`,
                },
            });
            //console.log(JSON.stringify(response.data.data.data));
            if (response && response.data && response.data.data) {
                setWingData(response.data.data.wing);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        }
    };

    // Function to fetch data from the API
    const fetchFloor = async (tower_id = '', wing_id = '') => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const formData = new FormData();
            formData.append("sortBy", 'id');
            formData.append('sortOrder', 'asc');
            formData.append('tower_id', tower_id);
            formData.append('wing_id', wing_id);
            const API_URL = appUrl + '/api/list-floor';
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "society_id": `${society_token}`,
                },
            });
            //console.log(JSON.stringify(response.data.data.data));
            if (response && response.data && response.data.data) {
                setFloorData(response.data.data.data);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        }
    };
    const handleTowerChange = (event, newValue) => {
        setSelectedTower(newValue);
        if(newValue){
        fetchWing(newValue.id);
        fetchFloor(newValue.id);
        formik.setFieldValue('tower_id', newValue.id);
        formik.setFieldValue('wing_id', '');
        setSelectedWing(null);
        formik.setFieldValue('floor_id', '');
        setSelectedFloor(null);
        }else{
          setWingData([]);
          formik.setFieldValue('wing_id', '');
          setSelectedWing(null);
          setFloorData([]);
          formik.setFieldValue('floor_id', '');
          setSelectedFloor(null);
        }
    };
    const handleWingChange = (event, newValue) => {
        setSelectedWing(newValue);
        if(newValue){
            formik.setFieldValue('wing_id', newValue.id);
            fetchFloor(formik.values.tower_id, newValue.id);
            formik.setFieldValue('floor_id', '');
            setSelectedFloor(null);
        }else{
            setFloorData([]);
            formik.setFieldValue('floor_id', '');
            setSelectedFloor(null);
        }
        
    };
    const handleFloorChange = (event, newValue) => {
        setSelectedFloor(newValue);
        formik.setFieldValue('floor_id', newValue.id);
    };

    useEffect(() => {
        if(id){
            // Find the Tower from the fetched list based on tower_id
            const selectedTowerData = towerOption.find(
            (tower) => tower.id === parseInt(formik.values.tower_id)
            );
            // If the tower is found, set it as the selectedTower
            if (selectedTowerData) {
            setSelectedTower(selectedTowerData);
            fetchWing(formik.values.tower_id);
            }
        }
    }, [towerOption, formik.values.tower_id]);

    useEffect(() => {
        if(id){
            const fetchWingUseEffect = async () => {
                try {
                    if (selectedTower) {
                        const appUrl = import.meta.env.VITE_API_URL;
                        const API_URL = `${appUrl}/api/show-tower/${formik.values.tower_id}`;
                        const response = await axios.get(API_URL, {
                            headers: {
                                Authorization: `Bearer ${token}`,
                                "Content-Type": "multipart/form-data",
                                "society_id": `${society_token}`,
                            },
                        });
                        // Update state options with the fetched data
                        setWingData(response.data.data.wing);
                        // Find the Data from the fetched list based on id
                        const selectedWingData = response.data.data.wing.find(
                            (wing) => wing.id === parseInt(formik.values.wing_id)
                        );
                        // If the data is found, set it as the selected
                        if (selectedWingData) {
                            setSelectedWing(selectedWingData);
                        }
                    }
                } catch (error) {
                    console.error("Error fetching data:", error);
                    setWingData([]); // Reset state data in case of an error
                }
            };
            fetchWingUseEffect();
        }
    }, [selectedTower, formik.values.wing_id]);

    useEffect(() => {
        if(id){
            const selectedFloorData = floorOption.find(
                (floor) => floor.id === parseInt(formik.values.floor_id)
            );
            // If the data is found, set it as the selected
            if (selectedFloorData) {
                setSelectedFloor(selectedFloorData);
            }
        }
    }, [selectedTower, selectedWing, formik.values.floor_id]);

     // Helper function to add a new name field
     const addMoreFields = () => {
        formik.setFieldValue('flat_name', [
        ...formik.values.flat_name,
        ''
        ]);
    };
    // Helper function to remove a name field
    const removeMoreFields = (index) => {
        const updatedNames = [...formik.values.flat_name];
        updatedNames.splice(index, 1);
        formik.setFieldValue('flat_name', updatedNames);
    };
    return (
        <>
            <PageContainer
                title="Parking"
                description="This is Parking"
            >
            <Breadcrumb title="" items={BCrumb} />
            {renderSuccessMessage()}
            {renderErrorMessage()}
            <ParentCard
                title={
                    id
                        ? "Edit Parking"
                        : "Add Parking"
                }
            >
                <form onSubmit={formik.handleSubmit} autoComplete="new-password">
                    <Grid container spacing={2}>
                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor="tower_id">Tower <span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="tower_id"
                            fullWidth
                            options={towerOption}
                            getOptionLabel={(option) => option.tower_name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedTower}
                            onChange={handleTowerChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.tower_name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Select"
                                aria-label="Tower"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.tower_id && Boolean(formik.errors.tower_id)}
                                helperText={formik.touched.tower_id && formik.errors.tower_id}
                                />
                            )}
                            />
                        </Grid>

                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor="wing_id">Wing</CustomFormLabel>
                            <Autocomplete
                            id="wing_id"
                            fullWidth
                            options={wingOption}
                            getOptionLabel={(option) => option.wings_name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedWing}
                            onChange={handleWingChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.wings_name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Select"
                                aria-label="Wing"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.wing_id && Boolean(formik.errors.wing_id)}
                                helperText={formik.touched.wing_id && formik.errors.wing_id}
                                />
                            )}
                            />
                        </Grid>

                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor="floor_id">Floor Number <span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="floor_id"
                            fullWidth
                            options={floorOption}
                            getOptionLabel={(option) => option.floor_name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedFloor}
                            onChange={handleFloorChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.floor_name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Select"
                                aria-label="Floor"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.floor_id && Boolean(formik.errors.floor_id)}
                                helperText={formik.touched.floor_id && formik.errors.floor_id}
                                />
                            )}
                            />
                        </Grid>
                        {id && (
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="flatname"
                                sx={{ mt: 4 }}
                            >
                                Flat Number <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="flatname"
                                name="flatname"
                                placeholder="Flat Number"
                                fullWidth
                                value={formik.values.flatname}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.flatname &&
                                    Boolean(formik.errors.flatname)
                                }
                                helperText={
                                    formik.touched.flatname &&
                                    formik.errors.flatname
                                }
                            />
                        </Grid>   
                        )}
                        {id == undefined && ( 
                        <Grid item xs={12}>
                            <CustomFormLabel htmlFor={`flat_name`} sx={{ mt: 0 }}>
                                Flats Number <span style={{color:"red"}}>*</span>
                            </CustomFormLabel>
                            <Grid container spacing={1} alignItems="center">        
                                {/* This one for inserting new one */}
                                {formik.values.flat_name.map((flatName, index) => (
                                    <Grid item xs={8} key={index} mt={1}>
                                        <Grid container spacing={1} alignItems="center">
                                            <Grid item xs={8}>
                                                <CustomTextField
                                                    id={`flat_name_${index}`}
                                                    name={`flat_name[${index}]`}
                                                    placeholder="Flat Name"
                                                    fullWidth
                                                    value={flatName}
                                                    onChange={formik.handleChange}
                                                    error={
                                                        formik.touched.flat_name &&
                                                        formik.touched.flat_name[index] &&
                                                        Boolean(formik.errors.flat_name) &&
                                                        Boolean(formik.errors.flat_name[index])
                                                    }
                                                    helperText={
                                                        formik.touched.flat_name &&
                                                        formik.touched.flat_name[index] &&
                                                        formik.errors.flat_name &&
                                                        formik.errors.flat_name[index]
                                                    }
                                                />
                                            </Grid>
                                            <Grid item xs={2} mt={(index === 0) ? 0 : 1}>
                                                {(index === 0) ? 
                                                    id ? 
                                                    <Button
                                                        variant="outlined"
                                                        color="error"
                                                        onClick={() => removeMoreFields(index)}
                                                        title="Remove"
                                                    >
                                                        <RemoveIcon />
                                                    </Button> : ''
                                                :
                                                    <Button
                                                        variant="outlined"
                                                        color="error"
                                                        onClick={() => removeMoreFields(index)}
                                                        title="Remove"
                                                    >
                                                        <RemoveIcon />
                                                    </Button>
                                                }
                                            </Grid>
                                        </Grid>
                                    </Grid>
                                ))}
                            </Grid>
                            <Grid item xs={12} mt={2}>
                                <Button
                                    variant="contained"
                                    color="success"
                                    onClick={addMoreFields}
                                    title="Add"
                                    style={{float:'right'}}
                                >
                                    <AddIcon />
                                    Add More
                                </Button>  
                            </Grid>                      
                        </Grid>        
                        )}
                        
                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/admin/parking-list">
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
                                color="primary"
                                type="submit"
                                disabled={isLoading}
                            >
                                {id ? "Update" : "Submit"}
                                {isLoading && <CircularProgress size={24} color="inherit" />}
                            </Button>
                        </Grid>
                    </Grid>
                </form>
                </ParentCard>
            </PageContainer>
        </>
    );
};

export default AddParking;